---
applyTo: "app/UseCases/**/*.php,tests/Unit/UseCases/**/*.php"
---
# UseCase Layer Rules

UseCase層は本システムにおける各ユースケース（機能単位）のビジネスロジックを担当する中核層です。

---

## 🎯 基本方針

### シングルアクションの採用

本プロジェクトでは**シングルアクション**を採用します：

- 公開メソッドは `__invoke()` のみ
- 1クラス1ユースケースを原則とする
- Controller と UseCase は 1:1 対応
- 「何を実現するのか」を表現する単位として設計する

### トランザクション管理の方針

**重要: トランザクション管理は UseCase 内で行います**

- ビジネスロジックとトランザクション境界が一致する
- UseCaseが独立してテスト可能になる
- トランザクション管理の責務がUseCaseに集約される

```php
// ✅ GOOD: UseCase内でトランザクション管理
public function __invoke(array $data): Dashboard
{
    return DB::transaction(function () use ($data) {
        $dashboard = $this->repository->create($data);
        DashboardCreated::dispatch($dashboard);
        return $dashboard;
    });
}
```

### CQRS パターン

- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）
- UseCaseはこれらを組み合わせてビジネスロジックを実装

---

## 責務

### ✅ UseCaseが行うべきこと

- ビジネスフローの制御
- トランザクション管理（DB::transaction）
- Query/Repository の組み合わせ
- Service でビジネスロジック実行（必要に応じて）
- ビジネスルールの検証
- イベントのディスパッチ（オプション）

### ❌ UseCaseが行ってはいけないこと

- HTTPリクエスト/レスポンスの処理
- ビュー/テンプレートの操作
- 直接的なDB操作（必ずQuery/Repository経由）
- 直接的な外部API呼び出し（Infrastructure経由）

---

## 命名規則

```
app/UseCases/{Feature}/{Action}UseCase.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Action}`: アクション（例: `Create`, `Update`, `Delete`）

### 具体例

```
app/UseCases/
├── Dashboard/
│   ├── CreateUseCase.php        # ダッシュボード作成
│   ├── UpdateUseCase.php        # ダッシュボード更新
│   └── DeleteUseCase.php        # ダッシュボード削除
├── Device/
│   ├── CreateUseCase.php
│   ├── UpdateUseCase.php
│   └── UpdateStatusUseCase.php  # ステータス更新
└── User/
    ├── CreateUseCase.php
    └── UpdateProfileUseCase.php # プロフィール更新
```

---

## 基本実装パターン

### 1. 作成処理のUseCase

```php
<?php

namespace App\UseCases\Dashboard;

use App\Events\Dashboard\DashboardCreated;
use App\Models\Dashboard;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateUseCase
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    /**
     * ダッシュボードを作成
     *
     * @param array<string, mixed> $data
     * @return Dashboard
     * @throws \Exception
     */
    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            // ビジネスルールの検証
            $this->validateBusinessRules($data);

            // ダッシュボードを作成
            $dashboard = $this->repository->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'layout' => $data['layout'],
                'is_public' => $data['is_public'] ?? false,
                'user_id' => $data['user_id'],
                'settings' => $data['settings'] ?? [],
            ]);

            Log::info('Dashboard created', [
                'dashboard_id' => $dashboard->id,
                'name' => $dashboard->name,
            ]);

            // イベントをディスパッチ（オプション）
            DashboardCreated::dispatch($dashboard);

            return $dashboard;
        });
    }

    /**
     * ビジネスルールの検証
     *
     * @param array<string, mixed> $data
     * @return void
     * @throws \Exception
     */
    private function validateBusinessRules(array $data): void
    {
        // レイアウトの妥当性チェック
        $allowedLayouts = ['grid', 'list', 'kanban'];
        if (!in_array($data['layout'], $allowedLayouts)) {
            throw new \Exception('無効なレイアウトが指定されました');
        }
    }
}
```

### 2. 更新処理のUseCase

```php
<?php

namespace App\UseCases\Dashboard;

use App\Events\Dashboard\DashboardUpdated;
use App\Models\Dashboard;
use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUseCase
{
    public function __construct(
        private DashboardQuery $query,
        private DashboardRepository $repository
    ) {}

    /**
     * ダッシュボードを更新
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Dashboard
     * @throws \Exception
     */
    public function __invoke(int $id, array $data): Dashboard
    {
        return DB::transaction(function () use ($id, $data) {
            // ダッシュボードの存在確認（Query使用）
            $dashboard = $this->query->findById($id);
            if (!$dashboard) {
                throw new \Exception('ダッシュボードが見つかりません');
            }

            // ビジネスルールの検証
            $this->validateBusinessRules($data);

            // 更新前のデータを保持
            $oldDashboard = clone $dashboard;

            // ダッシュボードを更新（Repository使用）
            $updatedDashboard = $this->repository->update($dashboard, [
                'name' => $data['name'] ?? $dashboard->name,
                'description' => $data['description'] ?? $dashboard->description,
                'layout' => $data['layout'] ?? $dashboard->layout,
                'is_public' => $data['is_public'] ?? $dashboard->is_public,
                'settings' => $data['settings'] ?? $dashboard->settings,
            ]);

            Log::info('Dashboard updated', [
                'dashboard_id' => $updatedDashboard->id,
                'changes' => array_keys($data),
            ]);

            // イベントをディスパッチ（オプション）
            DashboardUpdated::dispatch($updatedDashboard, $oldDashboard);

            return $updatedDashboard;
        });
    }

    /**
     * ビジネスルールの検証
     *
     * @param array<string, mixed> $data
     * @return void
     * @throws \Exception
     */
    private function validateBusinessRules(array $data): void
    {
        if (isset($data['layout'])) {
            $allowedLayouts = ['grid', 'list', 'kanban'];
            if (!in_array($data['layout'], $allowedLayouts)) {
                throw new \Exception('無効なレイアウトが指定されました');
            }
        }
    }
}
```

### 3. 削除処理のUseCase

```php
<?php

namespace App\UseCases\Dashboard;

use App\Events\Dashboard\DashboardDeleted;
use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteUseCase
{
    public function __construct(
        private DashboardQuery $query,
        private DashboardRepository $repository
    ) {}

    /**
     * ダッシュボードを削除
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function __invoke(int $id): void
    {
        DB::transaction(function () use ($id) {
            // ダッシュボードの存在確認（Query使用）
            $dashboard = $this->query->findById($id);
            if (!$dashboard) {
                throw new \Exception('ダッシュボードが見つかりません');
            }

            // 削除可否の確認
            $this->validateDeletion($dashboard);

            // ダッシュボードを削除（Repository使用）
            $this->repository->delete($dashboard);

            Log::info('Dashboard deleted', [
                'dashboard_id' => $id,
                'name' => $dashboard->name,
            ]);

            // イベントをディスパッチ（オプション）
            DashboardDeleted::dispatch($dashboard);
        });
    }

    /**
     * 削除可否を確認
     *
     * @param \App\Models\Dashboard $dashboard
     * @return void
     * @throws \Exception
     */
    private function validateDeletion($dashboard): void
    {
        // ビジネスルールに基づく削除制限
        // 例: システムデフォルトのダッシュボードは削除不可
        if ($dashboard->is_system_default) {
            throw new \Exception('システムデフォルトのダッシュボードは削除できません');
        }
    }
}
```

### 4. 複数のServiceを組み合わせるUseCase

```php
<?php

namespace App\UseCases\Dashboard;

use App\Events\Dashboard\DashboardDuplicated;
use App\Models\Dashboard;
use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use App\Services\Dashboard\DashboardDuplicationService;
use App\Services\Dashboard\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DuplicateUseCase
{
    public function __construct(
        private DashboardQuery $query,
        private DashboardRepository $repository,
        private DashboardDuplicationService $duplicationService,
        private NotificationService $notificationService
    ) {}

    /**
     * ダッシュボードを複製
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Dashboard
     * @throws \Exception
     */
    public function __invoke(int $id, array $data): Dashboard
    {
        return DB::transaction(function () use ($id, $data) {
            // 元のダッシュボードを取得（Query使用）
            $originalDashboard = $this->query->findById($id);
            if (!$originalDashboard) {
                throw new \Exception('ダッシュボードが見つかりません');
            }

            // 複製を作成（Service使用）
            $duplicatedData = $this->duplicationService->prepareDuplicateData(
                $originalDashboard,
                $data
            );

            // 新しいダッシュボードを作成（Repository使用）
            $newDashboard = $this->repository->create($duplicatedData);

            Log::info('Dashboard duplicated', [
                'original_id' => $id,
                'new_id' => $newDashboard->id,
            ]);

            // 通知を送信（Service使用）
            $this->notificationService->sendDuplicationNotification(
                $newDashboard,
                $originalDashboard
            );

            // イベントをディスパッチ（オプション）
            DashboardDuplicated::dispatch($newDashboard, $originalDashboard);

            return $newDashboard;
        });
    }
}
```

### 5. 一括処理のUseCase

```php
<?php

namespace App\UseCases\Dashboard;

use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkUpdateVisibilityUseCase
{
    public function __construct(
        private DashboardQuery $query,
        private DashboardRepository $repository
    ) {}

    /**
     * ダッシュボードの公開設定を一括更新
     *
     * @param array<int> $dashboardIds
     * @param bool $isPublic
     * @return int
     * @throws \Exception
     */
    public function __invoke(array $dashboardIds, bool $isPublic): int
    {
        return DB::transaction(function () use ($dashboardIds, $isPublic) {
            // バリデーション
            if (empty($dashboardIds)) {
                throw new \Exception('ダッシュボードIDが指定されていません');
            }

            $updatedCount = 0;

            foreach ($dashboardIds as $dashboardId) {
                $dashboard = $this->query->findById($dashboardId);

                if ($dashboard) {
                    $this->repository->update($dashboard, [
                        'is_public' => $isPublic,
                    ]);

                    $updatedCount++;
                }
            }

            $visibility = $isPublic ? '公開' : '非公開';
            Log::info("Dashboards visibility updated to {$visibility}", [
                'count' => $updatedCount,
                'dashboard_ids' => $dashboardIds,
            ]);

            return $updatedCount;
        });
    }
}
```

---

## Query と Repository の使い分け

### Query（読み取り専用）

データを読み取るだけの操作では**Query**を使用します：

```php
public function __construct(
    private DashboardQuery $query  // 読み取り
) {}

public function __invoke(int $id): Dashboard
{
    // Query を使用してデータを取得
    $dashboard = $this->query->findById($id);

    // 読み取ったデータを処理...
    return $dashboard;
}
```

### Repository（書き込み専用）

データを変更する操作では**Repository**を使用します：

```php
public function __construct(
    private DashboardRepository $repository  // 書き込み
) {}

public function __invoke(array $data): Dashboard
{
    return DB::transaction(function () use ($data) {
        // Repository を使用してデータを作成/更新/削除
        return $this->repository->create($data);
    });
}
```

### 両方を使用する場合

```php
public function __construct(
    private DashboardQuery $query,          // 読み取り
    private DashboardRepository $repository // 書き込み
) {}

public function __invoke(int $id, array $data): Dashboard
{
    return DB::transaction(function () use ($id, $data) {
        // Query で読み取り
        $dashboard = $this->query->findById($id);

        // Repository で更新
        return $this->repository->update($dashboard, $data);
    });
}
```

---

## イベントのディスパッチ（オプション）

イベントの使用は任意です。必要に応じて実装してください。

### UseCaseでのイベントディスパッチ

```php
use App\Events\Dashboard\DashboardCreated;

// ✅ 推奨: 静的dispatchメソッドを使用
DashboardCreated::dispatch($dashboard);

// ✅ 条件付きディスパッチ
DashboardCreated::dispatchIf($condition, $dashboard);
DashboardCreated::dispatchUnless($condition, $dashboard);

// ✅ トランザクション完了後にディスパッチ
// イベントクラスがShouldDispatchAfterCommitを実装している場合、
// トランザクションがコミットされてからディスパッチされます
```

---

## チェックリスト

- [ ] シングルアクション（`__invoke` のみ）になっているか
- [ ] 命名規約 `{Feature}/{Action}UseCase.php` に従っているか
- [ ] トランザクション管理はUseCase内で行われているか（DB::transaction）
- [ ] 読み取りはQueryを使用しているか
- [ ] 書き込みはRepositoryを使用しているか
- [ ] ビジネスロジックが適切に実装されているか
- [ ] 外部サービス連携はInfrastructure層に委譲されているか
- [ ] 共通ロジックはService層に委譲されているか
- [ ] 適切な例外処理が実装されているか
- [ ] イベントディスパッチは `::dispatch()` メソッドを使用しているか
- [ ] ログ出力が適切に行われているか
- [ ] PHPDocで型定義がされているか
- [ ] テストが書かれているか
