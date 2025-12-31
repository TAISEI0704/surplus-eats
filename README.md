# Surplus Eats Back-End

## プロジェクト概要

### 技術スタック

**バックエンド**
- Laravel 11.46.0
- PHP 8.4+
- PostgreSQL 18.1

**開発ツール**
- Docker / Docker Compose
- Mago (Linter/Formatter)
- PHPStan (静的解析)
- PHPUnit (テスト)

### アーキテクチャ

このプロジェクトは、UseCase中心のレイヤードアーキテクチャを採用しています：

- **Controller**: HTTPリクエスト受付とレスポンス返却（シングルアクション `__invoke` のみ）
- **Request**: バリデーション（FormRequest）
- **Resource**: APIレスポンス整形（JsonResource）
- **UseCase**: ビジネスロジックの実装（シングルアクション `__invoke` のみ）
- **Service**: 再利用可能なビジネスロジック（シングルアクション `__invoke` のみ）
- **Query**: データ読み取り専用（SELECT）
- **Repository**: データ更新専用（INSERT/UPDATE/DELETE）
- **Infrastructure**: 外部サービス連携（S3、外部API等）
- **Support**: ユーティリティ（純粋関数）
- **Model**: データ構造（Eloquent）

**CQRS パターン**: Query（読み取り）とRepository（書き込み）を明確に分離

詳細なアーキテクチャとコーディング規約は [AGENTS.md](./AGENTS.md)を参照してください。

### ドキュメント構成

本プロジェクトのドキュメントは、以下の階層構造で管理されています：

```
ドキュメント階層
│
├─ 開発ガイドライン（SSOT: 第一情報源）
│   ├─ AGENTS.md or CLAUDE.md             - アーキテクチャ、コーディング規約の統合ドキュメント
│   │   ├─ 5つの基本原則
│   │   ├─ レイヤ構造
│   │   ├─ 命名規則
│   │   └─ 絶対禁止事項
│   └─ .github/instructions/              - 詳細な実装ルール
│       ├─ architecture.instructions.md   - アーキテクチャ概要
│       ├─ backend.instructions.md        - バックエンド全般
│       └─ backend/                       - レイヤ別の詳細ルール
│           ├─ controller.instructions.md
│           ├─ usecase.instructions.md
│           ├─ query.instructions.md
│           ├─ repository.instructions.md
│           ├─ service.instructions.md
│           ├─ infrastructure.instructions.md
│           ├─ support.instructions.md
│           ├─ resource.instructions.md
│           ├─ request.instructions.md
│           ├─ testing.instructions.md
│           ├─ security.instructions.md
│           └─ performance.instructions.md
│
└─ プロジェクト情報
    └─ README.md (本ドキュメント)         - セットアップ、開発方法
```

**単一情報源（SSOT）ルール**:
- **アーキテクチャガイドライン**: `AGENTS.md` が第一情報源
- **詳細な実装ルール**: `.github/instructions/` 配下のファイル
- **矛盾時**: AGENTS.md → instructions → 実装コードの順で優先

新機能開発時の推奨読む順序: `AGENTS.md → 該当レイヤーの instructions`
## セットアップ方法

### 必要な環境
- Git
- gh CLI（GitHub公式CLIツール）
- Docker
- Docker Compose

### 初回セットアップ

1. リポジトリのクローン
```bash
git clone https://github.com/TAISEI0704/surplus-eats.git
cd surplus-eats
```

2. 環境変数の設定
```bash
cp .env.example .env
# infra/local/.env でWEB_USER_ID, WEB_GROUP_IDをホストのユーザID、グループIDに設定してください。
# 例：rootユーザの場合は0:0
WEB_USER_ID=0
WEB_GROUP_ID=0
# 例：一般ユーザの場合はidコマンドで確認した値を設定してください。
WEB_USER_ID=1000
WEB_GROUP_ID=1000
```

3. Dockerイメージのビルド
```bash
./bin/compose build --no-cache
```

4. Dockerコンテナの起動
```bash
./bin/compose up
```

初回起動時は、Dockerイメージがビルドされます。

5. アプリケーションのセットアップ
```bash
./bin/compose artisan migrate
```
または、コンテナ内で直接実行することもできます：
```bash
# コンテナに入る
./bin/compose exec web bash

# コンテナ内で実行
root@xxx:/app# php artisan migrate
```

アプリケーションは `http://localhost:8080` でアクセスできます。

### 開発サーバーの起動

Dockerコンテナを起動することで、自動的にLaravel開発サーバーが起動します：

```bash
./bin/compose up
```

コンテナを停止する場合：

```bash
./bin/compose stop
```

## デバッグ方法

### Laravelログの確認

Laravelのログファイルを確認できます：

```bash
# コンテナ内
tail -f storage/logs/laravel.log
```

または、Dockerログを確認：

```bash
./bin/compose logs -f app
```

### データベースの確認

PostgreSQLに直接接続してデータを確認できます：

```bash
./bin/compose exec db psql -U postgres -d surplus_eats
```

### デバッグコンテナの利用

開発用のコンテナに入って直接デバッグできます：

```bash
./bin/compose exec app bash
```

## テスト方法

### バックエンドテスト（PHPUnit）

すべてのテストを実行：

```bash
./bin/compose test
```

特定のテストファイルのみ実行：

```bash
./bin/compose test tests/Feature/ExampleTest.php
```

### コード品質チェック

#### コードフォーマット（Mago）

チェックのみ（修正なし）：

```bash
./bin/compose format:check
```

自動修正：

```bash
./bin/compose format
```

#### コードリント（Mago）

チェックのみ（修正なし）：

```bash
./bin/compose lint
```

自動修正：

```bash
./bin/compose lint:fix
```

#### 静的解析

PHPStan実行：

```bash
./bin/compose stan
```

Mago静的解析実行：

```bash
./bin/compose analyze
```

### すべてのチェックを実行

```bash
# フォーマットチェック
./bin/compose format:check

# リントチェック
./bin/compose lint

# 静的解析
./bin/compose stan
./bin/compose analyze
# テスト
./bin/compose test
```

## コーディング規約

このプロジェクトのコーディング規約とベストプラクティスは [AGENTS.md](./AGENTS.md) に詳細に記載されています。
新しいコードを書く前に必ず確認してください。

### クイックリファレンス

- **Controller**: シングルアクション（`__invoke`のみ）、薄く保つ
- **UseCase**: シングルアクション（`__invoke`のみ）、ビジネスロジックを実装
- **Query**: 読み取り専用（SELECT）、N+1解消
- **Repository**: 書き込み専用（INSERT/UPDATE/DELETE）
- **Service**: 再利用可能なビジネスロジック、 シングルアクション（`__invoke`のみ）
- **Infrastructure**: 外部サービス連携（S3、外部API等）
- **Support**: 純粋関数（副作用なし）

**絶対禁止事項**:
- ✗ マジックナンバー/マジックストリング
- ✗ 直接的なDB操作（必ずQuery/Repository経由）
- ✗ mixed型の無闇な使用
- ✗ エラーハンドリングの欠如
- ✗ SQLインジェクション/XSS脆弱性
- ✗ テストのないコード追加

詳細は [AGENTS.md](./AGENTS.md) および `.github/instructions/` を参照してください。

## その他

### コンテナの一覧確認

起動中のコンテナを確認：

```bash
./bin/compose ps
```

### コンテナのログ確認

特定のコンテナのログを確認：

```bash
# Webサーバー（Laravel）のログ
./bin/compose logs -f app

# データベースのログ
./bin/compose logs -f db
```

### コンテナの再ビルド

Dockerfileを変更した場合は、再ビルドが必要です：

```bash
./bin/compose build --no-cache
./bin/compose up -d
```

### データベースのリセット

データベースをリセットして再マイグレーション：

```bash
./bin/compose artisan migrate:fresh
```

シーダーも実行する場合：

```bash
./bin/compose artisan migrate:fresh --seed
```

### Composerパッケージの管理

新しいパッケージをインストール：

```bash
# コンテナ内でパッケージを追加
./bin/compose exec web composer require <package-name>
```

開発用パッケージをインストール：

```bash
./bin/compose exec web composer require --dev <package-name>
```

### よくあるトラブルシューティング

#### ポートが既に使用されている

他のアプリケーションがポートを使用している場合は、`.env` でポート番号を変更してください。

#### キャッシュのクリア

設定やルートのキャッシュをクリア：

```bash
./bin/compose artisan config:clear
./bin/compose artisan route:clear
./bin/compose artisan cache:clear
```

#### Composerのautoloadを再生成

クラスが見つからない場合：

```bash
./bin/compose exec app composer dump-autoload
```

#### 環境変数（.env）の変更が反映されない

`.env` ファイルを変更した場合、**appコンテナの再起動が必要**です：

```bash
# コンテナを再起動
./bin/compose stop app
./bin/compose up app
```

**重要**: Laravel Octane (FrankenPHP) は長時間動作するプロセスのため、環境変数はコンテナ起動時に読み込まれます。`config:clear` や `cache:clear` だけでは環境変数の変更は反映されません。

設定ファイル（`config/*.php`）のみを変更した場合は、以下のコマンドで反映できます：

```bash
./bin/compose artisan config:clear
./bin/compose artisan octane:reload
```
