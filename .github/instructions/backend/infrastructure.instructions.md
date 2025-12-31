---
applyTo: "app/Infrastructure/**/*.php,tests/Unit/Infrastructure/**/*.php"
---
# Infrastructure Layer Rules

Infrastructure層は外部システムとの通信を抽象化し、アプリケーションコアから外部依存を分離します。

---

## 🎯 基本方針

### Infrastructure層の役割

本プロジェクトでは**Infrastructure層**を使用して外部システムとの連携を管理します：

- **外部API**: AWS S3、SQS、Slack、サードパーティAPIとの通信
- **抽象化**: 外部システムの実装詳細をアプリケーションから隠蔽
- **テスト容易性**: Mockやスタブによるテストが容易
- **変更容易性**: 外部システムの変更がアプリケーションコアに影響しない

---

## 責務

### ✅ Infrastructureが行うべきこと

- 外部APIへのHTTPリクエスト
- AWS S3へのファイルアップロード/ダウンロード
- AWS SQSへのメッセージ送信
- Slack通知の送信
- サードパーティSDKのラッピング
- 外部システムのレスポンスをアプリケーション内部形式に変換

### ❌ Infrastructureが行ってはいけないこと

- ビジネスロジック（UseCaseの責務）
- データベース操作（Query/Repositoryの責務）
- トランザクション管理（UseCaseの責務）
- HTTPリクエストのバリデーション（FormRequestの責務）

---

## 命名規則

### 2つの組織化パターン

Infrastructure層は2つのパターンで組織化されます：

#### 1. 機能固有のInfrastructure

```
app/Infrastructure/{Feature}/{ExternalSystem}Client.php
```

**例:**
```
app/Infrastructure/
├── Dashboard/
│   ├── DashboardS3Client.php      # ダッシュボード画像のS3操作
│   └── DashboardSlackClient.php   # ダッシュボード通知
├── Device/
│   ├── DeviceApiClient.php        # デバイス外部API
│   └── DeviceSqsClient.php        # デバイスメッセージキュー
└── Karte/
    └── KarteS3Client.php          # カルテファイルのS3操作
```

#### 2. 共通/汎用的なInfrastructure

```
app/Infrastructure/{ExternalSystem}Client.php
```

**例:**
```
app/Infrastructure/
├── S3Client.php           # 汎用S3操作
├── SqsClient.php          # 汎用SQSメッセージング
├── SlackClient.php        # 汎用Slack通知
└── MailClient.php         # メール送信
```

---

## 基本実装パターン

### AWS S3クライアント

```php
<?php

namespace App\Infrastructure\Dashboard;

use Illuminate\Support\Facades\Storage;

class DashboardS3Client
{
    /**
     * ダッシュボード画像をS3にアップロード
     *
     * @param string $filePath
     * @param string $content
     * @return string S3のURL
     */
    public function uploadImage(string $filePath, string $content): string
    {
        Storage::disk('s3')->put($filePath, $content, 'public');

        return Storage::disk('s3')->url($filePath);
    }

    /**
     * ダッシュボード画像をS3から削除
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteImage(string $filePath): bool
    {
        return Storage::disk('s3')->delete($filePath);
    }

    /**
     * ダッシュボード画像のURLを取得
     *
     * @param string $filePath
     * @return string
     */
    public function getImageUrl(string $filePath): string
    {
        return Storage::disk('s3')->url($filePath);
    }

    /**
     * 一時的な署名付きURLを生成
     *
     * @param string $filePath
     * @param int $expirationMinutes
     * @return string
     */
    public function getTemporaryUrl(string $filePath, int $expirationMinutes = 60): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $filePath,
            now()->addMinutes($expirationMinutes)
        );
    }
}
```

### AWS SQSクライアント

```php
<?php

namespace App\Infrastructure\Device;

use Illuminate\Support\Facades\Queue;

class DeviceSqsClient
{
    /**
     * デバイスステータス更新メッセージを送信
     *
     * @param int $deviceId
     * @param string $status
     * @return void
     */
    public function sendStatusUpdate(int $deviceId, string $status): void
    {
        Queue::push('device.status.update', [
            'device_id' => $deviceId,
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * デバイスデータ同期メッセージを送信
     *
     * @param int $deviceId
     * @param array<string, mixed> $data
     * @return void
     */
    public function sendDataSync(int $deviceId, array $data): void
    {
        Queue::push('device.data.sync', [
            'device_id' => $deviceId,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

### Slackクライアント

```php
<?php

namespace App\Infrastructure\Dashboard;

use Illuminate\Support\Facades\Http;

class DashboardSlackClient
{
    public function __construct(
        private string $webhookUrl = ''
    ) {
        $this->webhookUrl = config('services.slack.webhook_url');
    }

    /**
     * ダッシュボード作成通知を送信
     *
     * @param int $dashboardId
     * @param string $dashboardName
     * @param string $createdBy
     * @return void
     */
    public function notifyDashboardCreated(
        int $dashboardId,
        string $dashboardName,
        string $createdBy
    ): void {
        $message = sprintf(
            '新しいダッシュボードが作成されました: %s (ID: %d) by %s',
            $dashboardName,
            $dashboardId,
            $createdBy
        );

        $this->sendMessage($message);
    }

    /**
     * Slackにメッセージを送信
     *
     * @param string $message
     * @return void
     */
    private function sendMessage(string $message): void
    {
        Http::post($this->webhookUrl, [
            'text' => $message,
        ]);
    }

    /**
     * リッチフォーマットでSlackにメッセージを送信
     *
     * @param string $title
     * @param string $message
     * @param string $color
     * @return void
     */
    public function sendFormattedMessage(
        string $title,
        string $message,
        string $color = 'good'
    ): void {
        Http::post($this->webhookUrl, [
            'attachments' => [
                [
                    'color' => $color,
                    'title' => $title,
                    'text' => $message,
                    'footer' => 'L-Gate Data Platform',
                    'ts' => now()->timestamp,
                ],
            ],
        ]);
    }
}
```

### 外部APIクライアント

```php
<?php

namespace App\Infrastructure\Device;

use Illuminate\Support\Facades\Http;

class DeviceApiClient
{
    public function __construct(
        private string $baseUrl = '',
        private string $apiKey = ''
    ) {
        $this->baseUrl = config('services.device_api.base_url');
        $this->apiKey = config('services.device_api.api_key');
    }

    /**
     * デバイス情報を外部APIから取得
     *
     * @param string $deviceId
     * @return array<string, mixed>
     */
    public function fetchDeviceInfo(string $deviceId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/devices/{$deviceId}");

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Failed to fetch device info: {$response->status()}"
            );
        }

        return $response->json();
    }

    /**
     * デバイスステータスを外部APIに送信
     *
     * @param string $deviceId
     * @param string $status
     * @return bool
     */
    public function updateDeviceStatus(string $deviceId, string $status): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->put("{$this->baseUrl}/devices/{$deviceId}/status", [
            'status' => $status,
        ]);

        return $response->successful();
    }

    /**
     * デバイスデータをバッチで取得
     *
     * @param array<string> $deviceIds
     * @return array<string, mixed>
     */
    public function fetchBatchDeviceData(array $deviceIds): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/devices/batch", [
            'device_ids' => $deviceIds,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Failed to fetch batch device data: {$response->status()}"
            );
        }

        return $response->json();
    }
}
```

---

## UseCaseでの使用例

### S3アップロードを含む作成処理

```php
<?php

namespace App\UseCases\Dashboard;

use App\Infrastructure\Dashboard\DashboardS3Client;
use App\Repositories\Dashboard\DashboardRepository;
use Illuminate\Support\Facades\DB;

class CreateWithImageUseCase
{
    public function __construct(
        private DashboardRepository $repository,
        private DashboardS3Client $s3Client
    ) {}

    /**
     * 画像付きダッシュボードを作成
     *
     * @param array<string, mixed> $data
     * @param string $imageContent
     * @return \App\Models\Dashboard
     */
    public function __invoke(array $data, string $imageContent): \App\Models\Dashboard
    {
        return DB::transaction(function () use ($data, $imageContent) {
            // S3に画像をアップロード
            $imagePath = "dashboards/{$data['name']}.png";
            $imageUrl = $this->s3Client->uploadImage($imagePath, $imageContent);

            // 画像URLを追加
            $data['image_url'] = $imageUrl;

            // ダッシュボードを作成
            return $this->repository->create($data);
        });
    }
}
```

### 外部API連携を含む同期処理

```php
<?php

namespace App\UseCases\Device;

use App\Infrastructure\Device\DeviceApiClient;
use App\Repositories\Device\DeviceRepository;
use Illuminate\Support\Facades\DB;

class SyncFromExternalApiUseCase
{
    public function __construct(
        private DeviceRepository $repository,
        private DeviceApiClient $apiClient
    ) {}

    /**
     * 外部APIからデバイス情報を同期
     *
     * @param string $externalDeviceId
     * @return \App\Models\Device
     */
    public function __invoke(string $externalDeviceId): \App\Models\Device
    {
        return DB::transaction(function () use ($externalDeviceId) {
            // 外部APIからデータを取得
            $externalData = $this->apiClient->fetchDeviceInfo($externalDeviceId);

            // 内部形式に変換
            $deviceData = [
                'name' => $externalData['name'],
                'type' => $externalData['type'],
                'status' => $externalData['status'],
                'external_id' => $externalDeviceId,
                'last_synced_at' => now(),
            ];

            // デバイスを作成または更新
            return $this->repository->createOrUpdate($deviceData);
        });
    }
}
```

---

## ベストプラクティス

### ✅ GOOD: 外部システムの詳細を隠蔽

```php
// Infrastructure層で外部システムの詳細を隠蔽
class DashboardS3Client
{
    public function uploadImage(string $filePath, string $content): string
    {
        Storage::disk('s3')->put($filePath, $content, 'public');
        return Storage::disk('s3')->url($filePath);
    }
}

// UseCaseは外部システムの詳細を知らない
class CreateUseCase
{
    public function __construct(private DashboardS3Client $s3Client) {}

    public function __invoke(array $data, string $image): Dashboard
    {
        $imageUrl = $this->s3Client->uploadImage($path, $image);
        // ...
    }
}
```

### ❌ BAD: UseCaseで直接外部システムを操作

```php
// UseCaseで直接S3を操作（NG）
class CreateUseCase
{
    public function __invoke(array $data, string $image): Dashboard
    {
        // 外部システムの詳細がUseCaseに漏れている
        Storage::disk('s3')->put($path, $image, 'public');
        $imageUrl = Storage::disk('s3')->url($path);
        // ...
    }
}
```

---

## エラーハンドリング

### リトライ処理

```php
<?php

namespace App\Infrastructure\Device;

use Illuminate\Support\Facades\Http;

class DeviceApiClient
{
    /**
     * リトライ付きでAPIを呼び出し
     *
     * @param string $deviceId
     * @return array<string, mixed>
     */
    public function fetchDeviceInfoWithRetry(string $deviceId): array
    {
        return retry(3, function () use ($deviceId) {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->baseUrl}/devices/{$deviceId}");

            if (!$response->successful()) {
                throw new \RuntimeException("API request failed");
            }

            return $response->json();
        }, 100); // 100ms間隔でリトライ
    }
}
```

---

## チェックリスト

- [ ] 命名規約に従っているか（機能固有 or 共通）
- [ ] 外部システムの詳細がアプリケーションコアに漏れていないか
- [ ] ビジネスロジックを含んでいないか
- [ ] 適切なエラーハンドリングを実装しているか
- [ ] リトライ処理が必要な場合は実装されているか
- [ ] タイムアウト設定が適切か
- [ ] 認証情報は環境変数から取得しているか
- [ ] PHPDocで型定義がされているか
- [ ] テストが書かれているか（Mockやスタブを使用）
- [ ] ログ出力が適切に行われているか
