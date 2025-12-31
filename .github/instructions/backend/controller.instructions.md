---
applyTo: "app/Http/Controllers/**/*.php,tests/Feature/Http/Controllers/**/*.php,tests/Unit/Http/Controllers/**/*.php"
---
# Controller Layer Rules

Controller層は薄く保ち、HTTPリクエストとアプリケーション内部ロジック（UseCase）との橋渡しのみを行います。

## 🎯 基本方針

### シングルアクションコントローラの採用

本プロジェクトでは**シングルアクションコントローラ**を採用します：

- 公開メソッドは `__invoke()` のみ
- Controller と UseCase を **1:1 対応**
- 1つのControllerは1つの責務のみを持つ
- RESTfulな複数アクション（index、store、show等）は別々のControllerに分離

---

## 責務

### ✅ Controllerが行うべきこと

1. **リクエストから必要なデータを取得**
   - FormRequestを通じてバリデーション済みデータを受け取る

2. **対応する UseCase を呼び出す**
   - Controller と UseCase は 1:1 対応
   - UseCaseの使い回しは行わない

3. **レスポンス形式に整形して返却**
   - API Resource または Inertia::render() でレスポンスを返す

### ❌ Controllerが行ってはいけないこと

- **ビジネスロジック**の記述
- **直接的なDB操作**
- **トランザクション管理**（UseCase内で行う）
- **外部API呼び出し**
- **複雑なデータ加工**

---

## 命名規則

```
app/Http/Controllers/{Feature}/{Action}Controller.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Action}`: アクション（例: `Create`, `Update`, `Delete`, `List`, `Show`）

### 具体例

```
app/Http/Controllers/
├── Dashboard/
│   ├── CreateController.php         # ダッシュボード作成
│   ├── UpdateController.php         # ダッシュボード更新
│   ├── DeleteController.php         # ダッシュボード削除
│   ├── ListController.php           # ダッシュボード一覧
│   └── ShowController.php           # ダッシュボード詳細
├── Device/
│   ├── CreateController.php
│   ├── UpdateStatusController.php   # ステータス更新
│   └── ListController.php
└── User/
    ├── CreateController.php
    └── UpdateProfileController.php  # プロフィール更新
```

---

## トランザクション管理

**重要: トランザクション管理は UseCase 内で行います**

Controller側ではトランザクション処理を行わず、UseCaseに委譲します。これにより：
- ビジネスロジックとトランザクション境界が一致する
- UseCaseが独立してテスト可能になる
- トランザクション管理の責務がUseCaseに集約される

```php
// ✅ GOOD: トランザクションはUseCase内
class CreateController extends Controller
{
    public function __construct(
        private CreateUseCase $useCase
    ) {}

    public function __invoke(CreateRequest $request)
    {
        // UseCaseがトランザクションを管理
        $dashboard = ($this->useCase)($request->validated());
        return new DashboardResource($dashboard);
    }
}

// ❌ BAD: Controller内でトランザクション
class CreateController extends Controller
{
    public function __invoke(CreateRequest $request)
    {
        $dashboard = DB::transaction(function () use ($request) {
            return ($this->useCase)($request->validated());
        });
        return new DashboardResource($dashboard);
    }
}
```

---

## 基本実装パターン

### 1. 作成（Create）

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CreateRequest;
use App\Http\Resources\Dashboard\DashboardResource;
use App\UseCases\Dashboard\CreateUseCase;

class CreateController extends Controller
{
    public function __construct(
        private CreateUseCase $useCase
    ) {}

    /**
     * ダッシュボード新規作成
     */
    public function __invoke(CreateRequest $request)
    {
        $dashboard = ($this->useCase)($request->validated());

        return new DashboardResource($dashboard);
    }
}
```

### 2. 更新（Update）

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateRequest;
use App\Http\Resources\Dashboard\DashboardResource;
use App\UseCases\Dashboard\UpdateUseCase;

class UpdateController extends Controller
{
    public function __construct(
        private UpdateUseCase $useCase
    ) {}

    /**
     * ダッシュボード更新
     */
    public function __invoke(UpdateRequest $request, int $id)
    {
        $dashboard = ($this->useCase)($id, $request->validated());

        return new DashboardResource($dashboard);
    }
}
```

### 3. 削除（Delete）

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\UseCases\Dashboard\DeleteUseCase;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(
        private DeleteUseCase $useCase
    ) {}

    /**
     * ダッシュボード削除
     */
    public function __invoke(int $id): JsonResponse
    {
        ($this->useCase)($id);

        return response()->json(['message' => 'ダッシュボードを削除しました'], 200);
    }
}
```

### 4. 一覧取得（List）

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
        $dashboards = $this->query->findAll();

        return DashboardResource::collection($dashboards);
    }
}
```

### 5. 詳細取得（Show）

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
    public function __invoke(int $id)
    {
        $dashboard = $this->query->findById($id);

        return new DashboardResource($dashboard);
    }
}
```

---

## ルーティング例

シングルアクションコントローラのルーティング設定例：

```php
<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;

// ダッシュボード
Route::prefix('dashboards')->group(function () {
    Route::get('/', Dashboard\ListController::class)->name('dashboards.index');
    Route::get('/{id}', Dashboard\ShowController::class)->name('dashboards.show');
    Route::post('/', Dashboard\CreateController::class)->name('dashboards.store');
    Route::put('/{id}', Dashboard\UpdateController::class)->name('dashboards.update');
    Route::delete('/{id}', Dashboard\DeleteController::class)->name('dashboards.destroy');
});

// デバイス
Route::prefix('devices')->group(function () {
    Route::get('/', Device\ListController::class)->name('devices.index');
    Route::post('/', Device\CreateController::class)->name('devices.store');
    Route::put('/{id}/status', Device\UpdateStatusController::class)->name('devices.status.update');
});
```

---

## ベストプラクティス

### ✅ GOOD: シングルアクションコントローラ

```php
// Controller と UseCase は 1:1 対応
class CreateController extends Controller
{
    public function __construct(
        private CreateUseCase $useCase
    ) {}

    public function __invoke(CreateRequest $request)
    {
        $dashboard = ($this->useCase)($request->validated());
        return new DashboardResource($dashboard);
    }
}
```

**メリット:**
- 責務が明確（1つのControllerは1つのアクション）
- テストが簡単
- ファイル名とクラス名からアクションが明確
- UseCaseとの対応が明確

### ❌ BAD: 複数アクションを持つController

```php
// 複数のアクションを持つ
class DashboardController extends Controller
{
    public function index() { /* ... */ }
    public function store(CreateRequest $request) { /* ... */ }
    public function show(int $id) { /* ... */ }
    public function update(UpdateRequest $request, int $id) { /* ... */ }
    public function destroy(int $id) { /* ... */ }
}
```

**デメリット:**
- 責務が肥大化
- UseCaseとの対応が不明瞭
- テストが複雑

---

## 読み取り vs 書き込み

### 読み取り系（Query使用）

一覧取得や詳細取得など、データを読み取るだけの操作では**Query**を使用します：

```php
class ListController extends Controller
{
    public function __construct(
        private DashboardQuery $query  // Query を使用
    ) {}

    public function __invoke()
    {
        $dashboards = $this->query->findAll();
        return DashboardResource::collection($dashboards);
    }
}
```

### 書き込み系（UseCase使用）

作成、更新、削除など、データを変更する操作では**UseCase**を使用します：

```php
class CreateController extends Controller
{
    public function __construct(
        private CreateUseCase $useCase  // UseCase を使用
    ) {}

    public function __invoke(CreateRequest $request)
    {
        $dashboard = ($this->useCase)($request->validated());
        return new DashboardResource($dashboard);
    }
}
```

---

## チェックリスト

- [ ] シングルアクションコントローラ（`__invoke` のみ）になっているか
- [ ] Controller と UseCase は 1:1 対応しているか
- [ ] 命名規約 `{Feature}/{Action}Controller.php` に従っているか
- [ ] Controllerは薄く保たれているか（処理フロー3ステップのみ）
- [ ] ビジネスロジックはUseCaseに委譲されているか
- [ ] トランザクション管理はUseCase内で行われているか
- [ ] FormRequestでバリデーションを行っているか
- [ ] レスポンスはResourceクラスで整形されているか
- [ ] 読み取り系はQueryを使用しているか
- [ ] 書き込み系はUseCaseを使用しているか
- [ ] 依存性は適切に注入されているか
- [ ] 適切なHTTPステータスコードを返しているか
- [ ] テストが書かれているか
