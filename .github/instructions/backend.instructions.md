---
applyTo: "**/*.php"
---
# Backend Coding Standards

Laravel のコーディング規約と実装パターンです。

## 📚 目次

このドキュメントは、コンテキスト圧迫を避けるため以下のレイヤー別ファイルに分割されています:

### プレゼンテーション層

- **[Controller (controller.instructions.md)](backend/controller.instructions.md)** - HTTPリクエストの受付とレスポンス返却
  - シングルアクションコントローラ (__invoke のみ)
  - Controller と UseCase は 1:1 対応
  - リクエストの受け取り
  - バリデーション (FormRequest経由)
  - UseCaseの呼び出し
  - レスポンスの返却

- **[Request (request.instructions.md)](backend/request.instructions.md)** - バリデーションロジック
  - FormRequestクラスの実装
  - バリデーションルール定義
  - カスタムバリデーション
  - エラーメッセージのカスタマイズ

- **[Resource (resource.instructions.md)](backend/resource.instructions.md)** - APIレスポンスの整形
  - JsonResourceの実装
  - データ変換とフォーマット
  - 条件付きフィールド
  - リレーションの含有

### ビジネスロジック層

- **[UseCase (usecase.instructions.md)](backend/usecase.instructions.md)** - ビジネスロジックの実装
  - シングルアクション (__invoke のみ)
  - 1クラス1ユースケースを原則
  - トランザクション管理
  - Query/Repository の組み合わせ
  - ビジネスルールの実装

- **[Service (service.instructions.md)](backend/service.instructions.md)** - ビジネスロジックの再利用
  - 複数のUseCaseから共通利用されるロジック
  - 肥大化した処理の切り出し
  - ビジネスロジックの再利用
  - ★ インフラ依存処理は Infrastructure へ

### データアクセス層

- **[Query (query.instructions.md)](backend/query.instructions.md)** - データ読み取り専用 (CQRS)
  - SELECT 専用のデータ取得ロジック
  - 画面表示・API レスポンス向けデータ整形
  - N+1 解消、JOIN の最適化
  - 読み取りロジックとビジネスロジックの分離

- **[Repository (repository.instructions.md)](backend/repository.instructions.md)** - データ更新専用 (CQRS)
  - INSERT / UPDATE / DELETE
  - データの更新処理を集約
  - Eloquent ORM の利用
  - テスタビリティの向上

### インフラ・補助層

- **[Infrastructure (infrastructure.instructions.md)](backend/infrastructure.instructions.md)** - 外部サービス連携
  - S3 などストレージサービス
  - 外部API・外部認証基盤
  - メール送信サービス
  - インフラ依存処理の集約

- **[Support (support.instructions.md)](backend/support.instructions.md)** - 共通ユーティリティ
  - 文字列・日付・配列操作
  - 純粋関数（副作用なし）
  - ビジネスロジック外の共通処理
  - テスト容易性の向上

### パフォーマンスとセキュリティ

- **[Performance (performance.instructions.md)](backend/performance.instructions.md)** - パフォーマンス最適化
  - データベース最適化 (N+1問題、インデックス)
  - キャッシュ戦略 (Redis、クエリキャッシュ)
  - クエリ最適化
  - ページネーション

- **[Security (security.instructions.md)](backend/security.instructions.md)** - セキュリティベストプラクティス
  - 認証・認可 (Policy)
  - CSRF対策
  - SQLインジェクション対策
  - Mass Assignment対策
  - パスワードセキュリティ
  - ファイルアップロード
  - レート制限

- **[Testing (testing.instructions.md)](backend/testing.instructions.md)** - テスト実装
  - Pest (Feature Test、Unit Test)
  - UseCase/Repository のテスト
  - E2Eテスト (Playwright)
  - CI/CD

---

## 🏗️ アーキテクチャ概要

```
┌─────────────────────────────────────────────────┐
│              プレゼンテーション層                │
├─────────────────────────────────────────────────┤
│  Controller (シングルアクション __invoke)        │
│    ↓ Request でバリデーション                   │
│    ↓ UseCase を呼び出し（1:1 対応）            │
│    ↓ Resource でレスポンス整形                  │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│              ビジネスロジック層                  │
├─────────────────────────────────────────────────┤
│  UseCase (シングルアクション __invoke)           │
│    ↓ トランザクション管理                       │
│    ↓ Query でデータ読み取り（SELECT）          │
│    ↓ Repository でデータ更新                    │
│    ↓ Service でビジネスロジック（必要時）       │
│                                                 │
│  Service (ビジネスロジック再利用)               │
│    - 複数 UseCase で共通利用                    │
│    - 肥大化した処理の切り出し                   │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│              データアクセス層                    │
├─────────────────────────────────────────────────┤
│  Query (読み取り専用)                           │
│    - SELECT / N+1 解消 / JOIN 最適化            │
│                                                 │
│  Repository (書き込み専用)                      │
│    - INSERT / UPDATE / DELETE                   │
│    ↓ Eloquent Model → Database                 │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│              インフラ・補助層                    │
├─────────────────────────────────────────────────┤
│  Infrastructure (外部サービス連携)              │
│    - S3 / 外部API / メール送信など             │
│                                                 │
│  Support (ユーティリティ)                       │
│    - 文字列・日付・配列操作など                 │
└─────────────────────────────────────────────────┘
```

**CQRS パターン:**
- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）

詳細は [architecture.instructions.md](architecture.instructions.md) を参照してください。

---

## 🚀 クイックスタート

### 新規機能実装の流れ (例: ダッシュボード作成)

```php
// 1. Request (バリデーション)
// app/Http/Requests/Dashboard/CreateRequest.php
class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}

// 2. UseCase (ビジネスロジック)
// app/UseCases/Dashboard/CreateUseCase.php
class CreateUseCase
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            return $this->dashboardRepository->create($data);
        });
    }
}

// 3. Controller (シングルアクション)
// app/Http/Controllers/Dashboard/CreateController.php
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

**ポイント:**
- Controller は `__invoke` のみ（シングルアクション）
- Controller と UseCase は 1:1 対応
- UseCase も `__invoke` を使用
- トランザクションは UseCase 内で管理
- 命名規約: `{Feature}/{Action}Controller.php`

---

## 📋 ディレクトリ構造

```
app/
├── Http/
│   ├── Controllers/          # Controller層（シングルアクション）
│   │   ├── Dashboard/        # 機能別に分類
│   │   │   ├── CreateController.php
│   │   │   ├── UpdateController.php
│   │   │   └── DeleteController.php
│   │   ├── Device/
│   │   ├── Karte/
│   │   └── User/
│   ├── Requests/             # バリデーション
│   │   └── Dashboard/
│   │       ├── CreateRequest.php
│   │       └── UpdateRequest.php
│   └── Resources/            # APIレスポンス整形
│       └── Dashboard/
│           └── DashboardResource.php
├── UseCases/                 # ビジネスロジック（シングルアクション）
│   └── Dashboard/
│       ├── CreateUseCase.php
│       ├── UpdateUseCase.php
│       └── DeleteUseCase.php
├── Services/                 # ビジネスロジック再利用
│   └── Dashboard/
│       └── NotificationService.php
├── Queries/                  # 読み取り専用（CQRS）
│   └── Dashboard/
│       └── DashboardQuery.php
├── Repositories/             # 書き込み専用（CQRS）
│   └── Dashboard/
│       └── DashboardRepository.php
├── Infrastructure/           # 外部サービス連携
│   ├── S3/
│   ├── ExternalApi/
│   └── Mail/
├── Support/                  # 共通ユーティリティ
│   ├── DateFormatter.php
│   ├── StringSanitizer.php
│   └── ArrayHelper.php
└── Models/                   # Eloquentモデル
    └── Dashboard.php
```

---

## 🎯 設計原則

### 1. 単一責任の原則 (SRP)

各レイヤーは明確に責任を分離:

| レイヤー | 責任 | やってはいけないこと |
|---------|------|---------------------|
| **Controller** | リクエスト受付とレスポンス返却 | ビジネスロジック、DB操作 |
| **Request** | バリデーション | ビジネスロジック |
| **UseCase** | ビジネスフローの実装 | DB操作の詳細 |
| **Service** | ビジネスロジックの再利用 | インフラ依存処理 |
| **Query** | データ読み取り（SELECT） | データ更新処理 |
| **Repository** | データ更新（INSERT/UPDATE/DELETE） | データ読み取り処理 |
| **Infrastructure** | 外部サービス連携 | ビジネスロジック |
| **Support** | ユーティリティ処理 | ビジネスロジック、副作用を持つ処理 |

### 2. 依存性逆転の原則 (DIP)

```php
// ✅ GOOD: インターフェースに依存
class CreateDashboardUseCase
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepository
    ) {}
}

// ❌ BAD: 具体的な実装に依存
class CreateDashboardUseCase
{
    public function __construct(
        private Dashboard $dashboard  // Eloquentモデルに直接依存
    ) {}
}
```

### 3. レイヤー間の依存方向

```
Controller → UseCase → Service → Infrastructure
                    ↘ Query → Model
                    ↘ Repository → Model

UseCase → Support (ユーティリティとして使用)
```

**逆方向の依存は禁止** (例: Repository → Controller は NG)

### 4. CQRS パターンの採用

- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）
- 責務分離により、将来的な読み取り用DB・書き込み用DBの分離にも対応可能

---

## 📖 参照ガイド

### よくあるタスク

| タスク | 参照先 |
|--------|--------|
| 新規エンドポイント作成 | [controller.instructions.md](backend/controller.instructions.md) |
| バリデーション追加 | [request.instructions.md](backend/request.instructions.md) |
| APIレスポンス整形 | [resource.instructions.md](backend/resource.instructions.md) |
| ビジネスロジック実装 | [usecase.instructions.md](backend/usecase.instructions.md) |
| 共通処理の実装 | [service.instructions.md](backend/service.instructions.md) |
| データアクセス最適化 | [repository.instructions.md](backend/repository.instructions.md) |
| パフォーマンス改善 | [performance.instructions.md](backend/performance.instructions.md) |
| セキュリティ対策 | [security.instructions.md](backend/security.instructions.md) |
| テスト実装 | [testing.instructions.md](backend/testing.instructions.md) |

---

## 🔗 関連ドキュメント

- **アーキテクチャ**: [architecture.instructions.md](architecture.instructions.md)

---

## 📝 命名規則クイックリファレンス

| 対象 | 規則 | パス例 |
|------|------|--------|
| Controller | `{Feature}/{Action}Controller.php` | `app/Http/Controllers/Dashboard/CreateController.php` |
| Request | `{Feature}/{Action}Request.php` | `app/Http/Requests/Dashboard/CreateRequest.php` |
| Resource | `{Feature}/{Entity}Resource.php` | `app/Http/Resources/Dashboard/DashboardResource.php` |
| UseCase | `{Feature}/{Action}UseCase.php` | `app/UseCases/Dashboard/CreateUseCase.php` |
| Service | `{Feature}/{WhatItDoes}Service.php` | `app/Services/Dashboard/NotificationService.php` |
| Query | `{Feature}/{Entity}Query.php` | `app/Queries/Dashboard/DashboardQuery.php` |
| Repository | `{Feature}/{Entity}Repository.php` | `app/Repositories/Dashboard/DashboardRepository.php` |
| Infrastructure | `{Feature}/{ServiceName}.php` | `app/Infrastructure/S3/S3StorageService.php` |
| Support | `{Feature}/{Name}.php` | `app/Support/DateFormatter.php` |

**命名パラメータ:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Action}`: アクション（例: `Create`, `Update`, `Delete`, `UpdateStatus`）
- `{Entity}`: エンティティ名（例: `Dashboard`, `Device`）
- `{WhatItDoes}`: 処理内容（例: `Notification`, `Validation`）

詳細は各レイヤーのドキュメントを参照してください。

---

## ✨ ベストプラクティス

### Controller（シングルアクション）

```php
// ✅ GOOD: シングルアクションコントローラ
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

// ❌ BAD: 複数のアクションを持つController
class DashboardController extends Controller
{
    public function store(CreateRequest $request) { /* ... */ }
    public function update(UpdateRequest $request, int $id) { /* ... */ }
    public function destroy(int $id) { /* ... */ }
}

// ❌ BAD: ビジネスロジックがController内に
public function __invoke(CreateRequest $request)
{
    $dashboard = Dashboard::create($request->validated());
    DashboardCreated::dispatch($dashboard);
    Notification::send($dashboard->user, new DashboardCreatedNotification($dashboard));
    return new DashboardResource($dashboard);
}
```

### UseCase（シングルアクション）

```php
// ✅ GOOD: __invoke を使用した単一責任
class CreateUseCase
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    public function __invoke(array $data): Dashboard
    {
        return DB::transaction(function () use ($data) {
            $dashboard = $this->repository->create($data);
            DashboardCreated::dispatch($dashboard);
            return $dashboard;
        });
    }
}

// ❌ BAD: 複数の責任を持つ
class DashboardUseCase
{
    public function create(array $data): Dashboard { /* ... */ }
    public function update(int $id, array $data): Dashboard { /* ... */ }
    public function delete(int $id): void { /* ... */ }
}
```

### Query / Repository の分離（CQRS）

```php
// ✅ GOOD: Query（読み取り専用）
class DashboardQuery
{
    public function findActive(): Collection
    {
        return Dashboard::where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

// ✅ GOOD: Repository（書き込み専用）
class DashboardRepository
{
    public function create(array $data): Dashboard
    {
        return Dashboard::create($data);
    }

    public function update(Dashboard $dashboard, array $data): Dashboard
    {
        $dashboard->update($data);
        return $dashboard;
    }
}

// ❌ BAD: Repository に読み取りと書き込みを混在
class DashboardRepository
{
    public function findActive(): Collection { /* ... */ }
    public function create(array $data): Dashboard { /* ... */ }
    public function update(Dashboard $dashboard, array $data): Dashboard { /* ... */ }
}

// ❌ BAD: Controllerで直接クエリ
public function __invoke()
{
    $dashboards = Dashboard::where('status', 'active')
        ->where('deleted_at', null)
        ->orderBy('created_at', 'desc')
        ->get();
}
```

### 条件分岐

```php
// ✅ GOOD: match式を使用（PHP 8.0+）
$status = match ($orderStatus) {
    'pending' => OrderStatus::PENDING,
    'processing' => OrderStatus::PROCESSING,
    'completed' => OrderStatus::COMPLETED,
    'cancelled' => OrderStatus::CANCELLED,
    default => throw new InvalidArgumentException("Invalid status: {$orderStatus}"),
};

// ✅ GOOD: match式で戻り値を直接返す
return match ($user->role) {
    'admin' => $this->adminRepository->findAll(),
    'manager' => $this->managerRepository->findByTeam($user->team_id),
    'user' => $this->userRepository->findOwn($user->id),
};

// ✅ GOOD: match式で複数条件をグループ化（カンマ区切り）
$accessLevel = match ($role) {
    'admin', 'superadmin', 'root' => AccessLevel::ADMINISTRATOR,
    'manager', 'supervisor' => AccessLevel::MANAGER,
    'user', 'guest' => AccessLevel::USER,
    default => AccessLevel::NONE,
};

// ❌ BAD: switch文を使用（冗長で型安全でない）
switch ($orderStatus) {
    case 'pending':
        $status = OrderStatus::PENDING;
        break;
    case 'processing':
        $status = OrderStatus::PROCESSING;
        break;
    case 'completed':
        $status = OrderStatus::COMPLETED;
        break;
    case 'cancelled':
        $status = OrderStatus::CANCELLED;
        break;
    default:
        throw new InvalidArgumentException("Invalid status: {$orderStatus}");
}
```

**match式のメリット:**
- 戻り値を直接返せる（式として扱える）
- 型安全（厳密な比較 `===` を使用）
- 全ケースを網羅していない場合はエラーになる（`UnhandledMatchError`）
- `break`が不要で簡潔
- カンマ区切りで複数条件をグループ化可能

**switch文を使うべきケース:**
- 各caseで複数行の処理が必要な場合
- 緩い型比較（`==`）が必要な場合
- fall-through（あるcaseの処理後に意図的に次のcaseも実行）が必要な場合
  - ただし、単に複数条件で同じ処理をする場合は`match`式のカンマ区切りを使用すること

---

このドキュメントは、プロジェクト全体のバックエンド規約の目次として機能します。各レイヤーの詳細は、対応するファイルを参照してください。
