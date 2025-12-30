# FrankenPHP + Laravel Octane セットアップ手順

このドキュメントでは、FrankenPHPとLaravel Octaneを使用した高速なLaravel環境のセットアップ手順を説明します。

## 概要

- **FrankenPHP**: CaddyとPHPを組み合わせた最新のアプリケーションサーバー
- **Laravel Octane**: Laravelアプリケーションを高速化するパッケージ
- **Docker Compose**: コンテナ化された開発環境

## 必要な環境

- Docker & Docker Compose
- Node.js & npm（フロントエンドアセット用）

## ディレクトリ構成

```
surplus-eats/
├── infra/
│   └── docker/
│       └── php/
│           ├── Dockerfile
│           ├── Caddyfile
│           └── startup-script.sh
├── config/
│   └── octane.php
├── .env
└── compose.yml
```

## セットアップ手順

### 1. Laravel Octaneのインストール

プロジェクトにLaravel Octaneをインストールします（既にインストール済みの場合はスキップ）：

```bash
composer require laravel/octane
php artisan octane:install --server=frankenphp
```

### 2. Dockerfileの作成

`infra/docker/php/Dockerfile`:

```dockerfile
FROM dunglas/frankenphp:1.10.1-php8.4-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

ARG WEB_USER_ID
ARG WEB_GROUP_ID

RUN usermod -u $WEB_USER_ID -o www-data
RUN groupmod -g $WEB_GROUP_ID -o www-data

RUN mkdir -p /data/caddy \
    && chown -R $WEB_USER_ID:$WEB_GROUP_ID /data

COPY ./Caddyfile /etc/frankenphp/Caddyfile

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install-php-extensions \
    intl \
    mbstring \
    pdo_pgsql \
    zip \
    bcmath \
    pcntl

WORKDIR /app

COPY ./startup-script.sh /startup-script.sh
RUN chmod +x /startup-script.sh

CMD ["/startup-script.sh"]
```

### 3. Caddyfileの作成

`infra/docker/php/Caddyfile`:

```caddyfile
{
    auto_https off
    admin off
    frankenphp {
        num_threads 16
    }
}

:8000 {
    root * /app/public
    encode gzip zstd
    php_server
}
```

**設定のポイント:**
- `auto_https off`: ローカル開発環境のためHTTPSを無効化
- `num_threads 16`: FrankenPHPのスレッド数（並行処理能力）
- `:8000`: コンテナ内でリッスンするポート番号

### 4. 起動スクリプトの作成

`infra/docker/php/startup-script.sh`:

```bash
#!/usr/bin/env sh
composer install --no-interaction --prefer-dist

echo "Starting Laravel Octane with FrankenPHP..."

# Run Laravel Octane
php artisan octane:frankenphp \
    --host=0.0.0.0 \
    --port=8000 \
    --workers=4 \
    --max-requests=500
```

**パラメータの説明:**
- `--host=0.0.0.0`: すべてのネットワークインターフェースでリッスン（Docker内部からアクセス可能にする）
- `--port=8000`: リッスンポート番号
- `--workers=4`: ワーカープロセス数（CPUコア数に応じて調整）
- `--max-requests=500`: ワーカーが再起動されるまでの最大リクエスト数（メモリリーク対策）

### 5. Docker Composeの作成

`compose.yml`:

```yaml
services:
  app:
    container_name: surplus-eats-app
    build:
      context: ./infra/docker/php
      dockerfile: Dockerfile
      args:
        - WEB_USER_ID=${WEB_USER_ID:-1000}
        - WEB_GROUP_ID=${WEB_GROUP_ID:-1000}
    user: "${WEB_USER_ID:-1000}:${WEB_GROUP_ID:-1000}"
    volumes:
      - ./:/app
    ports:
      - "${APP_PORT:-8080}:8000"

  postgres:
    container_name: surplus-eats-db
    image: postgres:18.1
    environment:
      - POSTGRES_DB=${DB_DATABASE:-surplus_eats}
      - POSTGRES_USER=${DB_USERNAME:-root}
      - POSTGRES_PASSWORD=${DB_PASSWORD:-password}
    volumes:
      - ./infra/docker/postgres:/var/lib/postgresql
    ports:
      - "${DB_PORT:-5432}:5432"
```

### 6. 環境変数の設定

`.env`ファイルに以下を追加します：

```env
# Octane設定
OCTANE_SERVER=frankenphp

# データベース設定
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=surplus_eats
DB_USERNAME=root
DB_PASSWORD=password
```

### 7. Octane設定の確認

`config/octane.php`のサーバー設定を確認します：

```php
'server' => env('OCTANE_SERVER', 'frankenphp'),
```

### 8. アプリケーションの起動

#### 初回セットアップ

```bash
# 1. Dockerイメージをビルド
docker compose build

# 2. コンテナを起動
docker compose up -d

# 3. アプリケーションキーを生成
docker compose exec app php artisan key:generate

# 4. データベースマイグレーション
docker compose exec app php artisan migrate

# 5. ストレージリンク作成
docker compose exec app php artisan storage:link

# 6. フロントエンドアセットをビルド
npm install
npm run build
```

#### 通常の起動

```bash
docker compose up -d
```

### 9. 動作確認

以下のコマンドで動作を確認します：

```bash
# HTTPステータスの確認
curl -I http://localhost:8080

# Octaneステータスの確認
docker compose exec app php artisan octane:status

# アプリケーションログの確認
docker compose logs app -f
```

正常に起動すると以下のような出力が表示されます：
```
Starting Laravel Octane with FrankenPHP...

   INFO  Server running….  

  Local: http://0.0.0.0:8000 

  Press Ctrl+C to stop the server
```

ブラウザで http://localhost:8080 にアクセスしてアプリケーションが表示されることを確認してください。

## 開発時の操作

### Octaneサーバーの再起動

コードを変更した場合は、以下のコマンドでサーバーを再起動します：

```bash
docker compose restart app
```

または、コンテナ内でリロード：

```bash
docker compose exec app php artisan octane:reload
```

### ワーカー数の調整

`startup-script.sh`の`--workers`パラメータを変更します：

```bash
# 例：8ワーカーに変更
--workers=8
```

### デバッグモード

開発時にホットリロードが必要な場合は、Octaneを使わずに通常のLaravel開発サーバーで起動できます：

```bash
# startup-script.shを一時的に変更
php artisan serve --host=0.0.0.0 --port=8000
```

### フロントエンド開発

Viteの開発サーバーを起動（ホットリロード有効）：

```bash
npm run dev
```

本番用アセットのビルド：

```bash
npm run build
```

## パフォーマンスチューニング

### ワーカー数の最適化

サーバーのCPUコア数に応じてワーカー数を調整します：

```bash
# 4コアCPU: --workers=4
# 8コアCPU: --workers=8
# 16コアCPU: --workers=16
```

推奨値：CPUコア数と同じか、コア数 × 1.5 程度

### max-requestsの調整

メモリリーク対策として、ワーカーを定期的に再起動します（デフォルト: 500）：

```bash
# より頻繁にワーカーを再起動（メモリ使用量が気になる場合）
--max-requests=250

# 再起動頻度を減らす（パフォーマンス優先の場合）
--max-requests=1000
```

### FrankenPHPのスレッド数

`Caddyfile`の`num_threads`を調整して同時接続数を増やせます：

```caddyfile
frankenphp {
    num_threads 32  # より多くの同時接続を処理可能
}
```

## トラブルシューティング

### エラー: Vite manifest not found

フロントエンドアセットがビルドされていない場合に発生します：

```bash
npm run build
```

### エラー: データベース接続失敗

`.env`の`DB_HOST`設定を確認してください：

```env
DB_HOST=postgres  # Docker Composeのサービス名と一致させる
```

### Octaneサーバーが起動しない

まずログを確認します：

```bash
docker compose logs app
```

権限エラーが発生している場合は、コンテナを再ビルドします：

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

### ポート番号の競合

ポート8080が既に使用されている場合は、`.env`で別のポートを指定します：

```env
APP_PORT=8081  # 8080が使用中の場合
```

## 本番環境への展開

### 環境変数の設定

本番環境用に以下の設定を行います：

```env
APP_ENV=production
APP_DEBUG=false
OCTANE_SERVER=frankenphp
```

### ワーカー数の増加

本番環境ではワーカー数を増やしてパフォーマンスを向上させます：

```bash
--workers=16
--max-requests=1000
```

### キャッシュの最適化

本番環境では各種キャッシュを有効化します：

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 参考資料

- [FrankenPHP公式ドキュメント](https://frankenphp.dev/)
- [Laravel Octane公式ドキュメント](https://laravel.com/docs/11.x/octane)
- [Caddy公式ドキュメント](https://caddyserver.com/docs/)

## この構成のメリット

FrankenPHP + Laravel Octaneの組み合わせにより、以下のメリットが得られます：

✅ **高速化**: 従来のPHP-FPMと比較して数倍高速なレスポンス  
✅ **メモリ効率**: アプリケーションをメモリ上に保持し、起動コストを削減  
✅ **並行処理**: 複数のワーカーで同時にリクエストを処理  
✅ **簡単なセットアップ**: Docker Composeでワンコマンド起動  
✅ **開発効率**: ホットリロードとビルドツールの統合で開発体験が向上  

---

**作成日**: 2025年12月7日  
**バージョン**: 1.0
