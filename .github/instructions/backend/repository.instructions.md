---
applyTo: "app/Repositories/**/*.php,tests/Unit/Repositories/**/*.php"
---
# Repository Layer Rules

Repository層はCQRSパターンの「Command（書き込み）」を担当し、データベースへのデータ更新専用の操作を提供します。

---

## 🎯 基本方針

### CQRSパターンにおけるRepository

本プロジェクトでは**CQRSパターン**を採用し、読み取り（Query）と書き込み（Repository）を分離します：

- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）
- 将来的に読み取り用DB・書き込み用DBを分離する場合にも対応しやすい構成

---

## 責務

### ✅ Repositoryが行うべきこと

- データの作成（INSERT）
- データの更新（UPDATE）
- データの削除（DELETE）
- リレーションの保存
- 永続化処理

### ❌ Repositoryが行ってはいけないこと

- データの読み取り（Queryの責務）
- ビジネスロジック（UseCaseの責務）
- トランザクション管理（UseCaseの責務）
- 外部API呼び出し（Infrastructureの責務）
- バリデーション（FormRequestの責務）

---

## 命名規則

```
app/Repositories/{Feature}/{Entity}Repository.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Entity}`: エンティティ名（例: `Dashboard`, `Device`, `User`）

### 具体例

```
app/Repositories/
├── Dashboard/
│   └── DashboardRepository.php       # ダッシュボード
├── Device/
│   └── DeviceRepository.php
├── Karte/
│   └── KarteRepository.php
└── User/
    ├── UserRepository.php
    └── UserProfileRepository.php     # ユーザープロフィール
```

---

## 基本実装パターン

### 基本的なRepository実装

```php
<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard;

class DashboardRepository
{
    /**
     * ダッシュボードを作成
     *
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function create(array $data): Dashboard
    {
        return Dashboard::create([
            'name' => $data['name'],
            'layout' => $data['layout'],
            'user_id' => $data['user_id'],
            'is_public' => $data['is_public'] ?? false,
        ]);
    }

    /**
     * ダッシュボードを更新
     *
     * @param Dashboard $dashboard
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function update(Dashboard $dashboard, array $data): Dashboard
    {
        $dashboard->update([
            'name' => $data['name'] ?? $dashboard->name,
            'layout' => $data['layout'] ?? $dashboard->layout,
            'is_public' => $data['is_public'] ?? $dashboard->is_public,
        ]);

        return $dashboard->fresh();
    }

    /**
     * ダッシュボードを削除
     *
     * @param Dashboard $dashboard
     * @return bool
     */
    public function delete(Dashboard $dashboard): bool
    {
        return $dashboard->delete();
    }

    /**
     * 複数のダッシュボードを一括作成
     *
     * @param array<int, array<string, mixed>> $dataList
     * @return void
     */
    public function bulkCreate(array $dataList): void
    {
        $records = array_map(function ($data) {
            return [
                'name' => $data['name'],
                'layout' => $data['layout'],
                'user_id' => $data['user_id'],
                'is_public' => $data['is_public'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $dataList);

        Dashboard::insert($records);
    }
}
```

### リレーション保存を含むRepository

```php
<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard;

class DashboardRepository
{
    /**
     * ダッシュボードとウィジェットを作成
     *
     * @param array<string, mixed> $dashboardData
     * @param array<int, array<string, mixed>> $widgetsData
     * @return Dashboard
     */
    public function createWithWidgets(array $dashboardData, array $widgetsData): Dashboard
    {
        $dashboard = Dashboard::create([
            'name' => $dashboardData['name'],
            'layout' => $dashboardData['layout'],
            'user_id' => $dashboardData['user_id'],
            'is_public' => $dashboardData['is_public'] ?? false,
        ]);

        // リレーションを保存
        foreach ($widgetsData as $widgetData) {
            $dashboard->widgets()->create([
                'type' => $widgetData['type'],
                'config' => $widgetData['config'],
                'position_x' => $widgetData['position_x'],
                'position_y' => $widgetData['position_y'],
            ]);
        }

        return $dashboard->fresh(['widgets']);
    }

    /**
     * ダッシュボードのウィジェットを更新
     *
     * @param Dashboard $dashboard
     * @param array<int, array<string, mixed>> $widgetsData
     * @return Dashboard
     */
    public function updateWidgets(Dashboard $dashboard, array $widgetsData): Dashboard
    {
        // 既存のウィジェットを削除
        $dashboard->widgets()->delete();

        // 新しいウィジェットを作成
        foreach ($widgetsData as $widgetData) {
            $dashboard->widgets()->create([
                'type' => $widgetData['type'],
                'config' => $widgetData['config'],
                'position_x' => $widgetData['position_x'],
                'position_y' => $widgetData['position_y'],
            ]);
        }

        return $dashboard->fresh(['widgets']);
    }

    /**
     * ダッシュボードの共有設定を更新
     *
     * @param Dashboard $dashboard
     * @param array<int> $userIds
     * @return Dashboard
     */
    public function updateShares(Dashboard $dashboard, array $userIds): Dashboard
    {
        // sync()を使用してリレーションを同期
        $dashboard->sharedUsers()->sync($userIds);

        return $dashboard->fresh(['sharedUsers']);
    }
}
```

### Mass Assignment対策

```php
<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard;

class DashboardRepository
{
    /**
     * ✅ GOOD: 明示的にフィールドを指定
     *
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function create(array $data): Dashboard
    {
        return Dashboard::create([
            'name' => $data['name'],
            'layout' => $data['layout'],
            'user_id' => $data['user_id'],
            'is_public' => $data['is_public'] ?? false,
        ]);
    }

    /**
     * ❌ BAD: リクエストデータをそのまま渡す（Mass Assignment脆弱性）
     *
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function createUnsafe(array $data): Dashboard
    {
        // 悪意のあるユーザーが予期しないフィールドを送信できる
        return Dashboard::create($data);
    }

    /**
     * ✅ GOOD: 更新時も明示的にフィールドを指定
     *
     * @param Dashboard $dashboard
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function update(Dashboard $dashboard, array $data): Dashboard
    {
        $dashboard->update([
            'name' => $data['name'] ?? $dashboard->name,
            'layout' => $data['layout'] ?? $dashboard->layout,
            'is_public' => $data['is_public'] ?? $dashboard->is_public,
        ]);

        return $dashboard->fresh();
    }
}
```

### デバイスRepository実装例

```php
<?php

namespace App\Repositories\Device;

use App\Models\Device;

class DeviceRepository
{
    /**
     * デバイスを作成
     *
     * @param array<string, mixed> $data
     * @return Device
     */
    public function create(array $data): Device
    {
        return Device::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'status' => $data['status'] ?? 'inactive',
            'location' => $data['location'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * デバイスを更新
     *
     * @param Device $device
     * @param array<string, mixed> $data
     * @return Device
     */
    public function update(Device $device, array $data): Device
    {
        $device->update([
            'name' => $data['name'] ?? $device->name,
            'type' => $data['type'] ?? $device->type,
            'status' => $data['status'] ?? $device->status,
            'location' => $data['location'] ?? $device->location,
            'metadata' => $data['metadata'] ?? $device->metadata,
        ]);

        return $device->fresh();
    }

    /**
     * デバイスステータスを更新
     *
     * @param Device $device
     * @param string $status
     * @return Device
     */
    public function updateStatus(Device $device, string $status): Device
    {
        $device->update([
            'status' => $status,
            'last_seen_at' => now(),
        ]);

        return $device->fresh();
    }

    /**
     * デバイスを削除
     *
     * @param Device $device
     * @return bool
     */
    public function delete(Device $device): bool
    {
        return $device->delete();
    }

    /**
     * デバイスを論理削除から復元
     *
     * @param int $deviceId
     * @return Device
     */
    public function restore(int $deviceId): Device
    {
        $device = Device::withTrashed()->findOrFail($deviceId);
        $device->restore();

        return $device->fresh();
    }

    /**
     * 複数デバイスのステータスを一括更新
     *
     * @param array<int> $deviceIds
     * @param string $status
     * @return int 更新された件数
     */
    public function bulkUpdateStatus(array $deviceIds, string $status): int
    {
        return Device::whereIn('id', $deviceIds)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }
}
```

---

## UseCaseでの使用例

### 作成処理（Repository使用）

```php
<?php

namespace App\UseCases\Dashboard;

use App\Models\Dashboard;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;

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
     */
    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            // Repositoryで作成
            return $this->repository->create($data);
        });
    }
}
```

### 更新処理（Query + Repository）

```php
<?php

namespace App\UseCases\Dashboard;

use App\Models\Dashboard;
use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;

class UpdateUseCase
{
    public function __construct(
        private DashboardQuery $query,          // 読み取り
        private DashboardRepository $repository // 書き込み
    ) {}

    /**
     * ダッシュボードを更新
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function __invoke(int $id, array $data): Dashboard
    {
        return DB::transaction(function () use ($id, $data) {
            // Query で読み取り
            $dashboard = $this->query->findById($id);

            if (!$dashboard) {
                throw new \Exception('ダッシュボードが見つかりません');
            }

            // Repository で更新
            return $this->repository->update($dashboard, $data);
        });
    }
}
```

### 削除処理（Query + Repository）

```php
<?php

namespace App\UseCases\Dashboard;

use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;

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
     */
    public function __invoke(int $id): void
    {
        DB::transaction(function () use ($id) {
            // Query で読み取り
            $dashboard = $this->query->findById($id);

            if (!$dashboard) {
                throw new \Exception('ダッシュボードが見つかりません');
            }

            // Repository で削除
            $this->repository->delete($dashboard);
        });
    }
}
```

### 複雑なビジネスロジックを含む作成処理

```php
<?php

namespace App\UseCases\Dashboard;

use App\Models\Dashboard;
use App\Repositories\Dashboard\DashboardRepository;
use App\Services\Dashboard\DashboardValidationService;
use Illuminate\Support\Facades\DB;

class CreateWithValidationUseCase
{
    public function __construct(
        private DashboardRepository $repository,
        private DashboardValidationService $validationService
    ) {}

    /**
     * バリデーション付きでダッシュボードを作成
     *
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            // ビジネスロジック（Serviceで実行）
            $this->validationService->validateDashboardLimit($data['user_id']);
            $this->validationService->validateLayoutConfig($data['layout']);

            // Repositoryで永続化
            return $this->repository->create($data);
        });
    }
}
```

---

## ベストプラクティス

### ✅ GOOD: fresh()で最新データを返す

```php
public function update(Dashboard $dashboard, array $data): Dashboard
{
    $dashboard->update([
        'name' => $data['name'],
        'layout' => $data['layout'],
    ]);

    // fresh()で最新データを取得
    return $dashboard->fresh();
}
```

### ❌ BAD: 古いデータを返す

```php
public function update(Dashboard $dashboard, array $data): Dashboard
{
    $dashboard->update([
        'name' => $data['name'],
        'layout' => $data['layout'],
    ]);

    // $dashboardには古いデータが残っている
    return $dashboard;
}
```

### ✅ GOOD: 明示的なフィールド指定

```php
public function create(array $data): Dashboard
{
    return Dashboard::create([
        'name' => $data['name'],
        'layout' => $data['layout'],
        'user_id' => $data['user_id'],
        'is_public' => $data['is_public'] ?? false,
    ]);
}
```

### ❌ BAD: データをそのまま渡す

```php
public function create(array $data): Dashboard
{
    // Mass Assignment脆弱性
    return Dashboard::create($data);
}
```

### ✅ GOOD: Repositoryは永続化のみ

```php
class DashboardRepository
{
    public function create(array $data): Dashboard
    {
        return Dashboard::create($data);
    }
}

// ビジネスロジックはUseCaseで実行
class CreateUseCase
{
    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            // ビジネスロジック
            if (count($this->query->findByUserId($data['user_id'])) >= 10) {
                throw new \Exception('ダッシュボードは10個まで');
            }

            return $this->repository->create($data);
        });
    }
}
```

### ❌ BAD: Repositoryにビジネスロジック

```php
class DashboardRepository
{
    public function create(array $data): Dashboard
    {
        // ビジネスロジックがRepositoryに混入（NG）
        if (count(Dashboard::where('user_id', $data['user_id'])->get()) >= 10) {
            throw new \Exception('ダッシュボードは10個まで');
        }

        return Dashboard::create($data);
    }
}
```

---

## トランザクション管理

**重要: トランザクション管理は UseCase で行います**

```php
// ✅ GOOD: UseCaseでトランザクション管理
class CreateUseCase
{
    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }
}

// ❌ BAD: Repository内でトランザクション
class DashboardRepository
{
    public function create(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            return Dashboard::create($data);
        });
    }
}
```

---

## テスト例

### Repositoryのユニットテスト

```php
<?php

namespace Tests\Unit\Repositories\Dashboard;

use App\Models\Dashboard;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private DashboardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new DashboardRepository();
    }

    /**
     * @test
     */
    public function ダッシュボードを作成できる(): void
    {
        $data = [
            'name' => 'テストダッシュボード',
            'layout' => 'grid',
            'user_id' => 1,
            'is_public' => true,
        ];

        $dashboard = $this->repository->create($data);

        $this->assertInstanceOf(Dashboard::class, $dashboard);
        $this->assertEquals('テストダッシュボード', $dashboard->name);
        $this->assertEquals('grid', $dashboard->layout);
        $this->assertTrue($dashboard->is_public);
    }

    /**
     * @test
     */
    public function ダッシュボードを更新できる(): void
    {
        $dashboard = Dashboard::factory()->create(['name' => '元の名前']);

        $updated = $this->repository->update($dashboard, [
            'name' => '新しい名前',
        ]);

        $this->assertEquals('新しい名前', $updated->name);
    }

    /**
     * @test
     */
    public function ダッシュボードを削除できる(): void
    {
        $dashboard = Dashboard::factory()->create();

        $result = $this->repository->delete($dashboard);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('dashboards', ['id' => $dashboard->id]);
    }
}
```

---

## チェックリスト

- [ ] 命名規約 `{Feature}/{Entity}Repository.php` に従っているか
- [ ] 書き込み専用の操作のみを実装しているか
- [ ] データの読み取りを行っていないか（Queryを使用）
- [ ] トランザクション管理をRepository内で行っていないか
- [ ] Mass Assignment脆弱性に対応しているか（明示的なフィールド指定）
- [ ] 更新後は`fresh()`で最新データを返しているか
- [ ] リレーションの保存が適切に実装されているか
- [ ] PHPDocで型定義がされているか
- [ ] テストが書かれているか
- [ ] ビジネスロジックを含んでいないか
