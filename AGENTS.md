# AGENTS.md

回答は日本語で行ってください。

Laravel を用いたバックエンドAPIサーバを構築するプロジェクトです。

---

## 技術スタック

- Backend: Laravel 11.46.0, PHP 8.4+
- Testing: PHPUnit
- Database: PostgreSQL 18.1

---

## 5つの基本原則

1. **レイヤ分離の遵守** - Controller/UseCase/Query/Repository/Service
2. **型安全性の確保** - PHP型定義、厳格な型宣言
3. **テストの同時生成** - PHPUnit による網羅的テスト
4. **セキュリティファースト** - 脆弱性のない安全な実装
5. **可読性・保守性** - 命名規則、コメント、ドキュメント

---

## レイヤ構造

```
Controller (薄く)
  ↓ リクエスト検証（FormRequest）、UseCase呼び出し、レスポンス整形（Resource）
UseCase (ビジネスオーケストレーション)
  ↓ 業務フロー、トランザクション管理
Query (読み取り専用)
  ↓ SELECT、データ取得、N+1対策
Repository (書き込み専用)
  ↓ INSERT/UPDATE/DELETE
Service (技術的処理)
  ↓ 共通処理、外部API連携、複雑な計算
Infrastructure (インフラ依存)
  ↓ S3、外部API、メール基盤
Support (ユーティリティ)
  ↓ ヘルパー関数、データ変換
Model (データ構造)
```

---

## 命名規則

**PHP**

- Controller: `{Feature}/{Action}Controller` (シングルアクション)
- UseCase: `{Feature}/{Action}UseCase`
- Query: `{Feature}/{Entity}Query`
- Repository: `{Feature}/{Entity}Repository`
- Service: `{Feature}/{Purpose}Service` または `{Purpose}Service`
- Resource: `{Feature}/{Entity}Resource`
- Request: `{Feature}/{Action}Request`

**例**
- `Dashboard/CreateController.php`
- `Dashboard/CreateUseCase.php`
- `Dashboard/DashboardQuery.php`
- `Dashboard/DashboardRepository.php`
- `Dashboard/DashboardResource.php`

---

## 絶対禁止事項

- ✗ マジックナンバー/マジックストリング
- ✗ 直接的なDB操作（必ずQuery/Repository経由）
- ✗ mixed型の無闇な使用
- ✗ エラーハンドリングの欠如
- ✗ SQLインジェクション/XSS脆弱性
- ✗ テストのないコード追加
- ✗ Controller にビジネスロジック
- ✗ Query層でINSERT/UPDATE/DELETE
- ✗ 秘密情報のハードコード

---

## 詳細な実装ルール

タスクに応じて以下のファイルを参照してください：

### Backend
- [アーキテクチャ](.github/instructions/architecture.instructions.md)
- [バックエンド全般](.github/instructions/backend.instructions.md)
- [Controller](.github/instructions/backend/controller.instructions.md)
- [UseCase](.github/instructions/backend/usecase.instructions.md)
- [Query](.github/instructions/backend/query.instructions.md)
- [Repository](.github/instructions/backend/repository.instructions.md)
- [Service](.github/instructions/backend/service.instructions.md)
- [Infrastructure](.github/instructions/backend/infrastructure.instructions.md)
- [Support](.github/instructions/backend/support.instructions.md)
- [Resource](.github/instructions/backend/resource.instructions.md)
- [Request](.github/instructions/backend/request.instructions.md)
- [Testing](.github/instructions/backend/testing.instructions.md)
- [Security](.github/instructions/backend/security.instructions.md)
- [Performance](.github/instructions/backend/performance.instructions.md)
