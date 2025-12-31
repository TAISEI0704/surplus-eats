---
applyTo: "app/Queries/**/*.php,tests/Unit/Queries/**/*.php"
---
# Query Layer Rules

Query層はCQRSパターンの「Query（読み取り）」を担当し、データベースからのデータ読み取り専用の操作を提供します。

---

## 🎯 基本方針

### CQRSパターンにおけるQuery

本プロジェクトでは**CQRSパターン**を採用し、読み取り（Query）と書き込み（Repository）を分離します：

- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）
- 将来的に読み取り用DB・書き込み用DBを分離する場合にも対応しやすい構成

---

## 責務

### ✅ Queryが行うべきこと

- データベースからのデータ読み取り（SELECT）
- 検索条件の構築
- ソート・ページネーション
- リレーションの読み込み
- 集計・グループ化
- キャッシュの利用（オプション）

### ❌ Queryが行ってはいけないこと

- データの作成・更新・削除（Repositoryの責務）
- ビジネスロジック（UseCaseの責務）
- トランザクション管理（UseCaseの責務）
- 外部API呼び出し（Infrastructureの責務）

---

## 命名規則

```
app/Queries/{Feature}/{Entity}Query.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Entity}`: エンティティ名（例: `Dashboard`, `Device`, `User`）

### 具体例

```
app/Queries/
├── Dashboard/
│   ├── DashboardQuery.php       # ダッシュボード
│   └── DashboardStatsQuery.php  # ダッシュボード統計
├── Device/
│   ├── DeviceQuery.php
│   └── DeviceStatusQuery.php    # デバイスステータス
├── Karte/
│   └── KarteQuery.php
└── User/
    ├── UserQuery.php
    └── UserProfileQuery.php     # ユーザープロフィール
```

---

## 基本実装パターン

### 基本的なQuery実装

```php
<?php

namespace App\Queries\Dashboard;

use App\Models\Dashboard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DashboardQuery
{
    /**
    * IDでダッシュボードを取得
    *
    * @param int $id
    * @return Dashboard|null
    */
    public function findById(int $id): ?Dashboard
    {
        return Dashboard::find($id);
    }

    /**
    * すべてのダッシュボードを取得
    *
    * @return Collection
    */
    public function findAll(): Collection
    {
        return Dashboard::orderBy('created_at', 'desc')->get();
    }

    /**
    * ページネーション付きで取得
    *
    * @param int $perPage
    * @return LengthAwarePaginator
    */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Dashboard::orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
    * ユーザーIDでダッシュボードを取得
    *
    * @param int $userId
    * @return Collection
    */
    public function findByUserId(int $userId): Collection
    {
        return Dashboard::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
    * 公開ダッシュボードを取得
    *
    * @return Collection
    */
    public function findPublic(): Collection
    {
        return Dashboard::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
    * 検索条件付きで取得
    *
    * @param array<string, mixed> $conditions
    * @return Collection
    */
    public function search(array $conditions): Collection
    {
        $query = Dashboard::query();

        if (isset($conditions['name'])) {
            $query->where('name', 'like', '%' . $conditions['name'] . '%');
        }

        if (isset($conditions['user_id'])) {
            $query->where('user_id', $conditions['user_id']);
        }

        if (isset($conditions['is_public'])) {
            $query->where('is_public', $conditions['is_public']);
        }

        if (isset($conditions['layout'])) {
            $query->where('layout', $conditions['layout']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
```

### リレーションを含むQuery

```php
<?php

namespace App\Queries\Dashboard;

use App\Models\Dashboard;
use Illuminate\Database\Eloquent\Collection;

class DashboardQuery
{
    /**
    * ユーザー情報を含めてダッシュボードを取得
    *
    * @param int $id
    * @return Dashboard|null
    */
    public function findByIdWithUser(int $id): ?Dashboard
    {
        return Dashboard::with('user')->find($id);
    }

    /**
    * 複数のリレーションを含めて取得
    *
    * @param int $id
    * @return Dashboard|null
    */
    public function findByIdWithRelations(int $id): ?Dashboard
    {
        return Dashboard::with([
            'user',
            'widgets',
            'shares',
        ])->find($id);
    }

    /**
    * ウィジェットを含めて全ダッシュボードを取得
    *
    * @return Collection
    */
    public function findAllWithWidgets(): Collection
    {
        return Dashboard::with('widgets')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
    * ユーザーとウィジェット数を含めて取得
    *
    * @return Collection
    */
    public function findAllWithUserAndWidgetCount(): Collection
    {
        return Dashboard::with('user')
            ->withCount('widgets')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

### 集計Query

```php
<?php

namespace App\Queries\Dashboard;

use App\Models\Dashboard;
use Illuminate\Support\Facades\DB;

class DashboardStatsQuery
{
    /**
    * ダッシュボード統計を取得
    *
    * @return array{total: int, public: int, private: int}
    */
    public function getStats(): array
    {
        return [
            'total' => Dashboard::count(),
            'public' => Dashboard::where('is_public', true)->count(),
            'private' => Dashboard::where('is_public', false)->count(),
        ];
    }

    /**
    * ユーザー別ダッシュボード数を取得
    *
    * @return \Illuminate\Support\Collection
    */
    public function countByUser(): \Illuminate\Support\Collection
    {
        return Dashboard::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
    * レイアウト別ダッシュボード数を取得
    *
    * @return array<string, int>
    */
    public function countByLayout(): array
    {
        return Dashboard::select('layout', DB::raw('count(*) as total'))
            ->groupBy('layout')
            ->get()
            ->pluck('total', 'layout')
            ->toArray();
    }

    /**
    * 月別作成数を取得
    *
    * @param int $months
    * @return \Illuminate\Support\Collection
    */
    public function countByMonth(int $months = 12): \Illuminate\Support\Collection
    {
        return Dashboard::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }
}
```

### デバイスQuery実装例

```php
<?php

namespace App\Queries\Device;

use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DeviceQuery
{
    /**
    * IDでデバイスを取得
    *
    * @param int $id
    * @return Device|null
    */
    public function findById(int $id): ?Device
    {
        return Device::find($id);
    }

    /**
    * ステータスでデバイスを取得
    *
    * @param string $status
    * @return Collection
    */
    public function findByStatus(string $status): Collection
    {
        return Device::where('status', $status)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
    * アクティブなデバイスを取得
    *
    * @return Collection
    */
    public function findActive(): Collection
    {
        return $this->findByStatus('active');
    }

    /**
    * 非アクティブなデバイスを取得
    *
    * @return Collection
    */
    public function findInactive(): Collection
    {
        return $this->findByStatus('inactive');
    }

    /**
    * タイプでフィルタリング
    *
    * @param string $type
    * @param int $perPage
    * @return LengthAwarePaginator
    */
    public function findByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return Device::where('type', $type)
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
    * 複合検索
    *
    * @param array<string, mixed> $filters
    * @return Collection
    */
    public function search(array $filters): Collection
    {
        $query = Device::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }
}
```

### デバイスステータスQuery実装例

```php
<?php

namespace App\Queries\Device;

use App\Models\Device;
use Illuminate\Support\Facades\DB;

class DeviceStatusQuery
{
    /**
    * ステータス別デバイス数を取得
    *
    * @return array<string, int>
    */
    public function countByStatus(): array
    {
        return Device::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
    * タイプ別デバイス数を取得
    *
    * @return array<string, int>
    */
    public function countByType(): array
    {
        return Device::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type')
            ->toArray();
    }

    /**
    * 平均アップタイムを取得
    *
    * @return float
    */
    public function getAverageUptime(): float
    {
        return (float) Device::where('status', 'active')
            ->avg('uptime') ?? 0;
    }

    /**
    * 最終更新が古いデバイスを取得
    *
    * @param int $hours
    * @return \Illuminate\Database\Eloquent\Collection
    */
    public function findStaleDevices(int $hours = 24): \Illuminate\Database\Eloquent\Collection
    {
        return Device::where('last_seen_at', '<', now()->subHours($hours))
            ->orderBy('last_seen_at', 'asc')
            ->get();
    }
}
```

---

## Controllerでの使用例

### 一覧取得（読み取り専用）

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\DashboardResource;
use App\Queries\Dashboard\DashboardQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListController extends Controller
{
    public function __construct(
        private DashboardQuery $query
    ) {}

    /**
    * ダッシュボード一覧取得
    */
    public function __invoke(): AnonymousResourceCollection
    {
        // Queryを使用してデータを読み取り
        $dashboards = $this->query->findAll();

        return DashboardResource::collection($dashboards);
    }
}
```

### 詳細取得（読み取り専用）

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\DashboardResource;
use App\Queries\Dashboard\DashboardQuery;

class ShowController extends Controller
{
    public function __construct(
        private DashboardQuery $query
    ) {}

    /**
    * ダッシュボード詳細取得
    */
    public function __invoke(int $id): DashboardResource
    {
        // Queryを使用してデータを読み取り
        $dashboard = $this->query->findByIdWithRelations($id);

        if (!$dashboard) {
            abort(404, 'ダッシュボードが見つかりません');
        }

        return new DashboardResource($dashboard);
    }
}
```

---

## UseCaseでの使用例

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

---

## パフォーマンス最適化

### N+1問題の回避

```php
// ❌ BAD: N+1問題が発生
public function findAll(): Collection
{
    return Dashboard::all(); // リレーションが読み込まれない
}

// ✅ GOOD: Eager Loadingで解決
public function findAllWithUser(): Collection
{
    return Dashboard::with('user')->get();
}
```

### インデックスの活用

```php
// インデックスが設定されたカラムでの検索
public function findByUserId(int $userId): Collection
{
    return Dashboard::where('user_id', $userId) // user_idにインデックス
        ->orderBy('created_at', 'desc')
        ->get();
}
```

### selectを使用した必要なカラムのみ取得

```php
public function findAllForList(): Collection
{
    return Dashboard::select(['id', 'name', 'layout', 'is_public', 'created_at'])
        ->orderBy('created_at', 'desc')
        ->get();
}
```

---

## チェックリスト

- [ ] 命名規約 `{Feature}/{Entity}Query.php` に従っているか
- [ ] 読み取り専用の操作のみを実装しているか
- [ ] データの作成・更新・削除を行っていないか
- [ ] N+1問題を回避しているか（with, withCount使用）
- [ ] 適切なインデックスを活用しているか
- [ ] ページネーションが必要な場合は実装されているか
- [ ] 検索条件が適切にバリデーションされているか
- [ ] PHPDocで型定義がされているか
- [ ] テストが書かれているか
- [ ] パフォーマンスが考慮されているか
