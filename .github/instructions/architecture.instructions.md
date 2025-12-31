---
applyTo: "**/*.php"
---
# Architecture Overview

Laravel プロジェクトのアーキテクチャ全体像です。

本システムのバックエンドは PHP フレームワーク **Laravel** を用いて構築します。Laravel が提供する標準機能・標準的な設計方針を尊重しつつ、**UseCase を中心としたレイヤ構成** を採用します。

## 🎯 設計方針

### 基本方針

1. **Laravel の機能を素直に活用する**
   - ルーティング、ミドルウェア、Eloquent ORM、バリデーションなど、Laravel が標準提供する機能を基本とする
   - フレームワークの思想と大きく乖離する実装は避け、学習コストや引き継ぎコストを抑える

2. **無理にフレームワーク依存を排除しない**
   - Laravel への依存をゼロにすることを目的とはしない
   - 将来的なフレームワーク更新やバージョンアップを想定しつつも、現実的な開発速度・運用負荷とのバランスを取る

3. **可読性・変更容易性を重視する**
   - 「崩れにくく、読みやすい」を設計の判断基準とする
   - ディレクトリ構成とクラスの責務を明確にし、機能追加や仕様変更時の影響範囲を局所化する

### クリーンアーキテクチャの原則

1. **レイヤー分離**: プレゼンテーション、ビジネスロジック、データアクセスを明確に分離
2. **依存性の方向**: 外側から内側への一方向（Controller → UseCase → Service/Repository）
3. **単一責任**: 各レイヤーは明確に定義された責任を持つ
4. **テスタビリティ**: 依存性注入により単体テストが容易

---

## 🏗️ レイヤー構造

```text
┌─────────────────────────────────────────────────┐
│         プレゼンテーション層 (外側)              │
├─────────────────────────────────────────────────┤
│  Controller (薄く保つ)                          │
│    ↓ シングルアクションコントローラ (__invoke)  │
│      リクエスト受付、バリデーション、           │
│      UseCaseの呼び出し、レスポンス返却         │
│    ★ Controller と UseCase は 1:1 対応         │
│                                                 │
│  Request (FormRequest)                          │
│    - 入力データのバリデーション・整形           │
│                                                 │
│  Resource (JsonResource)                        │
│    - 出力データの整形（API レスポンス）         │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│         ビジネスロジック層 (中間)                │
├─────────────────────────────────────────────────┤
│  UseCase (業務フローのオーケストレーション)      │
│    ↓ シングルアクション (__invoke のみ)         │
│      1クラス1ユースケースを原則                 │
│      トランザクション管理                       │
│      Query/Repository の組み合わせ             │
│      ビジネスルールの実装                       │
│                                                 │
│  Service (ロジック再利用・肥大化分離)           │
│    - 複数 UseCase から共通利用されるロジック    │
│    - ビジネスロジックの再利用                   │
│    ★ インフラ依存処理は Infrastructure へ      │
│                                                 │
│  Query (読み取り専用)                           │
│    - SELECT 専用のデータ取得ロジック            │
│    - 画面表示・API レスポンス向けデータ整形     │
│    - N+1 解消、JOIN の最適化                    │
│                                                 │
│  Repository (書き込み専用)                      │
│    - INSERT / UPDATE / DELETE                   │
│    - データの更新処理を集約                     │
│    ★ CQRS パターンによる責務分離               │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│         データアクセス層 (内側)                  │
├─────────────────────────────────────────────────┤
│  Model (Eloquent)                               │
│    - テーブル定義、リレーション                 │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│         インフラ・補助層                         │
├─────────────────────────────────────────────────┤
│  Infrastructure                                 │
│    - S3・外部API・外部認証基盤など              │
│    - インフラや外部サービスとの連携             │
│                                                 │
│  Support                                        │
│    - ビジネスロジック外の共通処理               │
│    - 文字列・日付・配列操作などユーティリティ   │
└─────────────────────────────────────────────────┘
```

**依存関係の方向:**
```text
Controller → UseCase → Service → Infrastructure
                    ↘ Query → Model
                    ↘ Repository → Model

UseCase → Support (ユーティリティとして使用)
```

**逆方向の依存は禁止** (例: Repository → Controller は NG)

---

## 📁 ディレクトリ構造

```text
app/
├── Console/
│   └── Commands/
├── Enums/                 # 列挙型（定数・マジックナンバーを管理）
├── Exceptions/            # カスタム例外（例外ハンドリング統一）
├── Http/
│   ├── Controllers/       # シングルアクションコントローラ
│   │   ├── Dashboard/     # 機能別に分類
│   │   ├── Device/
│   │   ├── Karte/
│   │   └── User/
│   ├── Middleware/        # リクエスト前後の共通処理
│   ├── Requests/          # FormRequest（入力バリデーション）
│   └── Resources/         # API Resource（レスポンス整形）
├── Models/                # Eloquent モデル
├── Providers/             # サービスプロバイダー
├── Queries/               # 読み取り専用（SELECT・画面用加工）
├── Repositories/          # 書き込み系（INSERT/UPDATE/DELETE）
├── Roles/                 # 権限判定（Policy の代替・補完）
├── Rules/                 # 共通バリデーションルールの再利用
├── Services/              # ロジック再利用・肥大化分離
├── UseCases/              # ユースケース（ビジネスフロー）
├── Support/               # ビジネスロジック外の共通処理（ユーティリティ）
└── Infrastructure/        # S3・外部APIなどインフラ依存の処理
```

### クラス命名規約

可読性と検索性向上のため、以下の命名規約を採用します。

```text
app/
 ├─ Http/
 │   ├─ Controllers/    #{Feature}/{Action}Controller.php   # __invoke のみ
 │   ├─ Requests/       #{Feature}/{Action}Request.php
 │   └─ Resources/      #{Feature}/{Entity}Resource.php
 ├─ UseCases/           #{Feature}/{Action}UseCase.php      # __invoke のみ
 ├─ Services/           #{Feature}/{WhatItDoes}Service.php  # __invoke のみ
 ├─ Queries/            #{Feature}/{Entity}Query.php
 ├─ Repositories/       #{Feature}/{Entity}Repository.php
 ├─ Roles/              #{Feature}/{Role}Role.php
 ├─ Rules/              #{Feature}/{Rule}Rule.php
 ├─ Support/            #{Feature}/{Name}.php
 ├─ Infrastructure/     #{Feature}/{ServiceName}.php
 ├─ Enums/              #{Feature}/{Name}.php
 ├─ Exceptions/         #{Feature}/{Name}Exception.php
 └─ Models/             #{Entity}.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Auth`, `Device`）
- `{Action}`: ユースケースの動詞（例: `Create`, `UpdateStatus`, `Delete`）
- `{Entity}`: 対象モデル名（例: `Dashboard`, `User`, `Device`）

---

## 🔄 データフロー

### リクエストからレスポンスまで

```
1. HTTPリクエスト
        ↓
2. Controller
   - Request でバリデーション
   - リクエストから必要なデータを取得
   - UseCase を呼び出し（Controller と UseCase は 1:1）
        ↓
3. UseCase
   - トランザクション開始 (DB::transaction)
   - Query でデータ読み取り（SELECT）
   - Service でビジネスロジック実行（必要に応じて）
   - Repository でデータ更新（INSERT/UPDATE/DELETE）
   - Infrastructure で外部サービス連携（必要に応じて）
   - Support でユーティリティ処理（必要に応じて）
   - トランザクション終了 (commit)
        ↓
4. Query
   - SELECT 文実行
   - N+1 解消、JOIN 最適化
   - 画面表示用データ整形
        ↓
5. Repository
   - INSERT / UPDATE / DELETE 実行
   - Eloquent を使ってDB操作
        ↓
6. Service
   - 複数 UseCase で共通利用されるロジック
   - 肥大化した処理の切り出し
        ↓
7. Infrastructure
   - S3 などストレージサービス
   - 外部API、外部認証基盤
   - メール送信サービス
        ↓
8. UseCase → Controller へ戻る
        ↓
9. Controller
   - Resource でレスポンス整形
   - JSON または Inertia::render() でレスポンス返却
        ↓
10. HTTPレスポンス (JSON or HTML + JSON)
```

### Controller 内の処理フロー

Controller 内にはビジネスロジックを記述せず、以下の処理のみに限定します：

1. リクエストから必要なデータを取得
2. 対応する UseCase を呼び出す
3. レスポンス形式（API Resource 等）に整形して返却する

---

## 📐 設計原則

### 1. 単一責任の原則 (SRP)

各レイヤーは一つの責任だけを持つ:

| レイヤー | 責任 | やってはいけないこと |
|---------|------|---------------------|
| Controller | リクエスト受付とレスポンス返却 | ビジネスロジック、DB操作 |
| Request | バリデーション | ビジネスロジック |
| UseCase | ビジネスフローの実装 | DB操作の詳細 |
| Service | 再利用可能なビジネスロジック | インフラ依存処理 |
| Query | データ読み取り（SELECT） | データ更新処理 |
| Repository | データ更新（INSERT/UPDATE/DELETE） | データ読み取り処理 |
| Infrastructure | 外部サービス連携 | ビジネスロジック |
| Support | ユーティリティ処理 | ビジネスロジック、副作用を持つ処理 |

### 2. 依存性逆転の原則 (DIP)

- **抽象に依存**: インターフェースに依存し、具体的な実装に依存しない
- **注入**: コンストラクタで依存を受け取る

```php
// ✅ GOOD: インターフェースに依存
class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private NotificationService $notificationService
    ) {}
}

// ❌ BAD: Eloquentモデルに直接依存
class CreateProductUseCase
{
    public function __construct(
        private Product $product
    ) {}
}
```

### 3. オープン・クローズドの原則 (OCP)

- **拡張に開いている**: 新機能追加時に既存コードを変更しない
- **変更に閉じている**: インターフェースは安定

例: 新しい通知方法（Slack）を追加する場合、`NotificationService` を拡張するだけで、既存の UseCase は変更不要

---

## 🏛️ アーキテクチャ手法との関係

### DDD（ドメイン駆動設計）

本システムでは**フルスケールの DDD は採用しない**。

**理由:**
- Eloquent ORM との完全な分離が難しく、DDD のメリットを最大限活かしにくい
- 頻繁な要件変更が想定されるほどドメインが複雑ではない
- 開発チーム全員に高度な DDD の理解を求めるコストが高い

### DTO（Data Transfer Object）

**原則として明示的な DTO 導入は行わない**。

- Laravel の Request / Resource によって入出力整形を行うことで多くのケースをカバーする
- ただし、入力項目が非常に多い・複雑なユースケースにおいては、型保証・層間疎結合の観点から DTO を導入する選択肢も残す

### CQRS（Command Query Responsibility Segregation）

**概念としては採用する**。

- 実装としては、読み取り専用の **Query 層** と、更新専用の **Repository 層** を分けることで責務分離を実現する
- 将来的に読み取り用 DB・書き込み用 DB を分離する場合にも対応しやすい構成とする

---

## 🔗 詳細ドキュメント

### Backend

- **全体概要**: [backend.instructions.md](backend.instructions.md)
- **Controller**: [backend/controller.instructions.md](backend/controller.instructions.md)
- **Request**: [backend/request.instructions.md](backend/request.instructions.md)
- **Resource**: [backend/resource.instructions.md](backend/resource.instructions.md)
- **UseCase**: [backend/usecase.instructions.md](backend/usecase.instructions.md)
- **Service**: [backend/service.instructions.md](backend/service.instructions.md)
- **Repository**: [backend/repository.instructions.md](backend/repository.instructions.md)
- **Performance**: [backend/performance.instructions.md](backend/performance.instructions.md)
- **Security**: [backend/security.instructions.md](backend/security.instructions.md)
- **Testing**: [backend/testing.instructions.md](backend/testing.instructions.md)

---

## 🚀 実装例

各レイヤーの実装例は、上記の詳細ドキュメントを参照してください。

### 簡単な例: 商品作成の流れ

```
1. POST /products ← HTTPリクエスト

2. ProductController@__invoke (シングルアクション)
   ↓ CreateProductRequest でバリデーション
   ↓ CreateProductUseCase を呼び出し（1:1 対応）

3. CreateProductUseCase@__invoke
   ↓ DB::transaction() 開始
   ↓ ProductRepository->create() でDB保存
   ↓ NotificationService->sendCreated() で通知
   ↓ commit()

4. ProductController@__invoke
   ↓ ProductResource でレスポンス整形
   ↓ return new ProductResource($product)

5. JSON レスポンス → クライアント
```

詳細な実装例は [backend/usecase.instructions.md](backend/usecase.instructions.md) を参照してください。

---

## 🎯 期待される効果

上記のアーキテクチャ設計により、以下の効果を期待します：

1. **影響範囲の局所化**
   - 機能単位（UseCase 単位）での影響範囲が明確になり、仕様変更時の改修コストが抑えられる

2. **API インタフェースの見通し向上**
   - Controller が薄く保たれることで API インタフェースの見通しが良くなり、フロントエンドとの連携が行いやすくなる

3. **保守性・テスト容易性の向上**
   - Query / Repository / Infrastructure / Support などのレイヤ分離により、以下が混在しないため、保守性・テスト容易性が向上する：
     - 読み取り・書き込み
     - 外部サービス依存
     - 共通ユーティリティ

4. **開発効率とコード品質のバランス**
   - Laravel の標準機能を活用しつつ、適切なレイヤ分離により、学習コストを抑えながらコード品質を維持できる

---

このドキュメントは、プロジェクト全体のアーキテクチャの鳥瞰図を提供します。各レイヤーの詳細な実装パターンやベストプラクティスは、対応するドキュメントを参照してください。
