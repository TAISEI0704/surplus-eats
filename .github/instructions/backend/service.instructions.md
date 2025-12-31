---
applyTo: "app/Services/**/*.php,tests/Unit/Services/**/*.php"
---
# Service Layer Rules

Service層は技術的な処理を担当し、外部API連携、ファイル操作、複雑な計算処理などを提供します。

---

## 🎯 基本方針

### シングルアクションの採用

本プロジェクトでは**シングルアクション**を採用します：

- 公開メソッドは `__invoke()` のみ
- 1クラス1処理を原則とする
- 「何をするのか」を明確に表現する単位として設計する
- 複数の処理が必要な場合は、クラスを分割する

### Service層の役割

本プロジェクトでは**Service層**を使用して技術的処理をカプセル化します：

- **技術的処理**: 外部API連携、ファイル操作、画像処理など
- **複雑な計算**: 統計計算、集計処理、データ変換
- **再利用性**: 複数のUseCaseから利用可能
- **分離**: ビジネスロジックとインフラストラクチャの分離

### ServiceとInfrastructureの違い

- **Service**: ビジネスドメインに関連する処理（ダッシュボード複製、通知など）
- **Infrastructure**: 外部システムとの純粋な通信（AWS S3 Client、Slack Clientなど）

---

## 責務

### ✅ Serviceが行うべきこと

- 外部API/サービスとの通信
- ファイルアップロード/ダウンロード
- 画像処理・変換
- メール送信
- PDF生成
- 複雑な計算・集計処理
- データ変換・サニタイズ
- 共通的な技術処理

### ❌ Serviceが行ってはいけないこと

- ビジネスロジックの制御（UseCaseの責務）
- 直接的なDB操作（Repositoryの責務）
- HTTPリクエスト/レスポンスの処理（Controllerの責務）

---

## 命名規則

**Service層は2つのパターンで組織化されます:**

#### 1. 機能固有のService

```
app/Services/{Feature}/{Purpose}Service.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Purpose}`: サービスの目的（例: `Duplication`, `Notification`, `Calculation`）

**具体例:**
```
app/Services/
├── Dashboard/
│   ├── DashboardDuplicationService.php    # ダッシュボード複製
│   ├── NotificationService.php            # 通知送信
│   └── AnalyticsCalculationService.php    # 分析計算
├── Device/
│   ├── StatusSyncService.php              # ステータス同期
│   └── DataAggregationService.php         # データ集計
└── Karte/
    ├── ExportService.php                  # エクスポート
    └── ImportService.php                  # インポート
```

#### 2. 共通/汎用的なService

```
app/Services/{Purpose}Service.php
```

**命名例:**
```
app/Services/
├── ImageUploadService.php          # 画像アップロード
├── EmailNotificationService.php    # メール送信
├── PdfGenerationService.php        # PDF生成
├── ExternalApiService.php          # 外部API連携
└── FileStorageService.php          # ファイルストレージ
```

---

## 基本実装パターン

### 1. 機能固有のService

#### ダッシュボード複製Service

```php
<?php

namespace App\Services\Dashboard;

use App\Models\Dashboard;
use Illuminate\Support\Facades\Log;

class PrepareDuplicateDataService
{
    /**
     * ダッシュボード複製用のデータを準備
     *
     * @param Dashboard $originalDashboard
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public function __invoke(Dashboard $originalDashboard, array $overrides = []): array
    {
        $duplicatedData = [
            'name' => $overrides['name'] ?? $originalDashboard->name . ' (コピー)',
            'description' => $overrides['description'] ?? $originalDashboard->description,
            'layout' => $originalDashboard->layout,
            'is_public' => $overrides['is_public'] ?? false,
            'user_id' => $overrides['user_id'] ?? $originalDashboard->user_id,
            'settings' => $this->duplicateSettings($originalDashboard->settings),
        ];

        Log::info('Dashboard duplicate data prepared', [
            'original_id' => $originalDashboard->id,
            'new_name' => $duplicatedData['name'],
        ]);

        return $duplicatedData;
    }

    /**
     * 設定を複製（必要に応じて調整）
     *
     * @param array<string, mixed> $originalSettings
     * @return array<string, mixed>
     */
    private function duplicateSettings(array $originalSettings): array
    {
        // 設定の深いコピーを作成
        $duplicatedSettings = $originalSettings;

        // 必要に応じて設定を調整
        if (isset($duplicatedSettings['widgets'])) {
            $duplicatedSettings['widgets'] = array_map(function ($widget) {
                // ウィジェットIDをリセット
                unset($widget['id']);
                return $widget;
            }, $duplicatedSettings['widgets']);
        }

        return $duplicatedSettings;
    }
}
```

#### ダッシュボード複製通知Service

```php
<?php

namespace App\Services\Dashboard;

use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Notifications\DashboardDuplicatedNotification;

class SendDuplicationNotificationService
{
    /**
     * ダッシュボード複製通知を送信
     *
     * @param Dashboard $newDashboard
     * @param Dashboard $originalDashboard
     * @return void
     */
    public function __invoke(Dashboard $newDashboard, Dashboard $originalDashboard): void
    {
        $user = User::find($newDashboard->user_id);

        if (!$user) {
            Log::warning('User not found for dashboard duplication notification', [
                'user_id' => $newDashboard->user_id,
            ]);
            return;
        }

        try {
            $user->notify(new DashboardDuplicatedNotification($newDashboard, $originalDashboard));

            Log::info('Dashboard duplication notification sent', [
                'user_id' => $user->id,
                'new_dashboard_id' => $newDashboard->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send dashboard duplication notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

#### デバイスデータ集計Service

```php
<?php

namespace App\Services\Device;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AggregateDeviceDataService
{
    /**
     * デバイスデータを集計
     *
     * @param Collection $devices
     * @param string $startDate
     * @param string $endDate
     * @return array{total: int, active: int, inactive: int, averageUptime: float}
     */
    public function __invoke(Collection $devices, string $startDate, string $endDate): array
    {
        $total = $devices->count();
        $active = $devices->where('status', 'active')->count();
        $inactive = $devices->where('status', 'inactive')->count();

        $averageUptime = $devices
            ->pluck('uptime')
            ->filter()
            ->avg() ?? 0;

        $result = [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'averageUptime' => round($averageUptime, 2),
        ];

        Log::info('Device data aggregated', [
            'period' => "{$startDate} - {$endDate}",
            'total' => $total,
        ]);

        return $result;
    }
}
```

---

### 2. 共通/汎用的なService

### 画像アップロードService

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadImageService
{
    /**
     * 画像をアップロード
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return array{path: string, url: string}
     */
    public function __invoke(UploadedFile $file, string $directory = 'images'): array
    {
        // ファイル名を生成
        $filename = $this->generateFilename($file);

        // S3にアップロード
        $path = Storage::disk('s3')->putFileAs(
            $directory,
            $file,
            $filename,
            'public'
        );

        return [
            'path' => $path,
            'url' => Storage::disk('s3')->url($path),
        ];
    }

    /**
     * ユニークなファイル名を生成
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::uuid() . '.' . $extension;
    }
}
```

> **注意**: 画像削除が必要な場合は、別のService(`DeleteImageService`)を作成してください。

### 外部APIからユーザー取得Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class FetchUserFromExternalApiService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;
    private int $retryTimes;

    public function __construct()
    {
        $this->baseUrl = config('services.external_api.base_url');
        $this->apiKey = config('services.external_api.api_key');
        $this->timeout = config('services.external_api.timeout', 10);
        $this->retryTimes = config('services.external_api.retry_times', 3);
    }

    /**
     * ユーザー情報を取得
     *
     * @param int $userId
     * @return array<string, mixed>
     * @throws RequestException
     */
    public function __invoke(int $userId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retryTimes, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/users/{$userId}");

            $response->throw();

            Log::info('External API: User fetched successfully', [
                'user_id' => $userId,
            ]);

            return $response->json();

        } catch (RequestException $e) {
            Log::error('External API: Failed to fetch user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
```

> **注意**: データ送信など他のAPI操作が必要な場合は、別のService(`SendDataToExternalApiService`など)を作成してください。

### ダッシュボード共有メール送信Service

```php
<?php

namespace App\Services;

use App\Mail\DashboardShared;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDashboardSharedEmailService
{
    /**
     * ダッシュボード共有メールを送信
     *
     * @param Dashboard $dashboard
     * @param User $recipient
     * @return bool
     */
    public function __invoke(Dashboard $dashboard, User $recipient): bool
    {
        try {
            Mail::to($recipient->email)
                ->send(new DashboardShared($dashboard, $recipient));

            Log::info('Email sent: Dashboard shared', [
                'dashboard_id' => $dashboard->id,
                'recipient_email' => $recipient->email,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send dashboard shared email', [
                'dashboard_id' => $dashboard->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
```

> **注意**: パスワードリセットメールなど他のメール送信が必要な場合は、別のService(`SendPasswordResetEmailService`など)を作成してください。

### PDF生成Service

```php
<?php

namespace App\Services;

use App\Models\Dashboard;
use App\Models\Karte;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfGenerationService
{
    /**
     * ダッシュボードレポートPDFを生成
     *
     * @param Dashboard $dashboard
     * @param array<string, mixed> $data
     * @return string PDFファイルのパス
     */
    public function generateDashboardReportPdf(Dashboard $dashboard, array $data): string
    {
        $pdf = Pdf::loadView('pdfs.dashboard-report', [
            'dashboard' => $dashboard,
            'data' => $data,
        ]);

        $filename = "dashboard_report_{$dashboard->id}_" . now()->format('YmdHis') . ".pdf";
        $path = "reports/{$filename}";

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('Dashboard report PDF generated', [
            'dashboard_id' => $dashboard->id,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * カルテPDFを生成
     *
     * @param Karte $karte
     * @return string PDFファイルのパス
     */
    public function generateKartePdf(Karte $karte): string
    {
        $pdf = Pdf::loadView('pdfs.karte', [
            'karte' => $karte,
        ]);

        $filename = "karte_{$karte->id}.pdf";
        $path = "kartes/{$filename}";

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('Karte PDF generated', [
            'karte_id' => $karte->id,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * PDFをダウンロード可能なレスポンスとして返す
     *
     * @param Dashboard $dashboard
     * @param array<string, mixed> $data
     * @return \Illuminate\Http\Response
     */
    public function downloadDashboardReportPdf(Dashboard $dashboard, array $data): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdfs.dashboard-report', [
            'dashboard' => $dashboard,
            'data' => $data,
        ]);

        $filename = "dashboard_report_{$dashboard->id}.pdf";

        return $pdf->download($filename);
    }
}
```

### 計算処理Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Collection;

class StatisticsCalculationService
{
    /**
     * 平均値を計算
     *
     * @param Collection $values
     * @return float
     */
    public function calculateAverage(Collection $values): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        return round($values->avg(), 2);
    }

    /**
     * 中央値を計算
     *
     * @param Collection $values
     * @return float
     */
    public function calculateMedian(Collection $values): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        $sorted = $values->sort()->values();
        $count = $sorted->count();
        $middle = (int) floor($count / 2);

        if ($count % 2 === 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return $sorted[$middle];
    }

    /**
     * パーセンタイルを計算
     *
     * @param Collection $values
     * @param int $percentile
     * @return float
     */
    public function calculatePercentile(Collection $values, int $percentile): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        $sorted = $values->sort()->values();
        $count = $sorted->count();
        $index = (int) ceil(($percentile / 100) * $count) - 1;

        return $sorted[$index] ?? 0;
    }

    /**
     * 標準偏差を計算
     *
     * @param Collection $values
     * @return float
     */
    public function calculateStandardDeviation(Collection $values): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        $mean = $values->avg();
        $variance = $values->reduce(function ($carry, $value) use ($mean) {
            return $carry + pow($value - $mean, 2);
        }, 0) / $values->count();

        return round(sqrt($variance), 2);
    }

    /**
     * 成長率を計算
     *
     * @param float $current
     * @param float $previous
     * @return float パーセンテージ
     */
    public function calculateGrowthRate(float $current, float $previous): float
    {
        if ($previous == 0) {
            return 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
```

### 画像処理Service

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageProcessingService
{
    /**
     * 画像をリサイズしてアップロード
     *
     * @param UploadedFile $file
     * @param array{width: int, height: int} $dimensions
     * @param string $directory
     * @return array{path: string, url: string}
     */
    public function resizeAndUpload(UploadedFile $file, array $dimensions, string $directory = 'images'): array
    {
        $image = Image::make($file);

        // アスペクト比を保持してリサイズ
        $image->fit($dimensions['width'], $dimensions['height']);

        $filename = uniqid() . '.jpg';
        $path = "{$directory}/{$filename}";

        // S3にアップロード
        Storage::disk('s3')->put(
            $path,
            (string) $image->encode('jpg', 85),
            'public'
        );

        return [
            'path' => $path,
            'url' => Storage::disk('s3')->url($path),
        ];
    }

    /**
     * サムネイルを生成
     *
     * @param string $imagePath
     * @return array{path: string, url: string}
     */
    public function generateThumbnail(string $imagePath): array
    {
        $image = Image::make(Storage::disk('s3')->get($imagePath));

        $image->fit(200, 200);

        $thumbnailPath = str_replace('.', '_thumb.', $imagePath);

        Storage::disk('s3')->put(
            $thumbnailPath,
            (string) $image->encode('jpg', 85),
            'public'
        );

        return [
            'path' => $thumbnailPath,
            'url' => Storage::disk('s3')->url($thumbnailPath),
        ];
    }
}
```

### データ変換Service

```php
<?php

namespace App\Services;

class DataTransformService
{
    /**
     * CSVデータを配列に変換
     *
     * @param string $csvContent
     * @return array<int, array<string, mixed>>
     */
    public function csvToArray(string $csvContent): array
    {
        $lines = explode("\n", trim($csvContent));
        $headers = str_getcsv(array_shift($lines));
        $data = [];

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $values = str_getcsv($line);
            $data[] = array_combine($headers, $values);
        }

        return $data;
    }

    /**
     * 配列をCSVに変換
     *
     * @param array<int, array<string, mixed>> $data
     * @return string
     */
    public function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        // ヘッダー行
        fputcsv($output, array_keys($data[0]));

        // データ行
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
```

---

## UseCaseからの利用例

```php
<?php

namespace App\UseCases\Dashboard;

use App\Models\Dashboard;
use App\Queries\Dashboard\DashboardQuery;
use App\Repositories\Dashboard\DashboardRepository;
use App\Services\Dashboard\PrepareDuplicateDataService;
use App\Services\Dashboard\SendDuplicationNotificationService;
use Illuminate\Support\Facades\DB;

class DuplicateUseCase
{
    public function __construct(
        private DashboardQuery $query,
        private DashboardRepository $repository,
        private PrepareDuplicateDataService $prepareDuplicateDataService,
        private SendDuplicationNotificationService $sendNotificationService
    ) {}

    /**
     * ダッシュボードを複製
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Dashboard
     */
    public function __invoke(int $id, array $data): Dashboard
    {
        return DB::transaction(function () use ($id, $data) {
            // Query で元のダッシュボードを取得
            $originalDashboard = $this->query->findById($id);

            // Serviceで複製データを準備(__invokeを呼び出し)
            $duplicatedData = ($this->prepareDuplicateDataService)(
                $originalDashboard,
                $data
            );

            // Repositoryで新しいダッシュボードを作成
            $newDashboard = $this->repository->create($duplicatedData);

            // Serviceで通知を送信(__invokeを呼び出し)
            ($this->sendNotificationService)(
                $newDashboard,
                $originalDashboard
            );

            return $newDashboard;
        });
    }
}
```

---

## ベストプラクティス

### ✅ GOOD: シングルアクションで機能固有のServiceを使う

```php
// Dashboard機能固有の処理
namespace App\Services\Dashboard;

class PrepareDuplicateDataService
{
    public function __invoke(Dashboard $dashboard, array $overrides): array
    {
        // ダッシュボード複製に特化した処理
        return [
            'name' => $overrides['name'] ?? $dashboard->name . ' (コピー)',
            'layout' => $dashboard->layout,
            'settings' => $this->duplicateSettings($dashboard->settings),
        ];
    }

    private function duplicateSettings(array $settings): array
    {
        // ダッシュボード固有の設定複製ロジック
        // ...
    }
}
```

### ❌ BAD: 複数のメソッドを持つService

```php
// シングルアクション原則に違反
namespace App\Services;

class CommonService
{
    // ❌ 複数のメソッド(__invoke以外)がある
    public function duplicateDashboard($dashboard, $data) { /* ... */ }
    public function duplicateDevice($device, $data) { /* ... */ }
    public function sendEmail($to, $subject) { /* ... */ }
    public function uploadFile($file) { /* ... */ }
    // 1クラス1処理の原則に違反
}
```

> **正しい実装**: それぞれ別のServiceクラスに分割し、`__invoke`を使用してください。

### ✅ GOOD: ログとエラーハンドリングを適切に実装

```php
public function sendData(array $data): array
{
    try {
        $response = Http::timeout($this->timeout)
            ->retry($this->retryTimes, 1000)
            ->post("{$this->baseUrl}/data", $data);

        $response->throw();

        Log::info('External API: Data sent successfully', [
            'data_id' => $response->json('id'),
        ]);

        return $response->json();

    } catch (RequestException $e) {
        Log::error('External API: Failed to send data', [
            'error' => $e->getMessage(),
            'data' => $data,
        ]);

        throw $e;
    }
}
```

### ❌ BAD: エラーハンドリングなし

```php
public function sendData(array $data): array
{
    // エラーハンドリングなし
    $response = Http::post("{$this->baseUrl}/data", $data);
    return $response->json();
}
```

### ✅ GOOD: 環境変数から設定を読み込む

```php
public function __construct()
{
    $this->baseUrl = config('services.external_api.base_url');
    $this->apiKey = config('services.external_api.api_key');
    $this->timeout = config('services.external_api.timeout', 10);
}
```

### ❌ BAD: ハードコーディング

```php
public function __construct()
{
    $this->baseUrl = 'https://api.example.com'; // ハードコーディング
    $this->apiKey = 'secret-key-12345'; // セキュリティリスク
}
```

### ✅ GOOD: Serviceは技術的処理のみ(__invokeを使用)

```php
class UploadImageService
{
    // 技術的処理のみ
    public function __invoke(UploadedFile $file, string $directory): array
    {
        $filename = $this->generateFilename($file);
        $path = Storage::disk('s3')->putFileAs($directory, $file, $filename);
        return ['path' => $path, 'url' => Storage::disk('s3')->url($path)];
    }

    private function generateFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }
}

// ビジネスロジックはUseCaseで実行
class CreateDashboardWithImageUseCase
{
    public function __construct(
        private UploadImageService $uploadImageService,
        private DashboardRepository $repository
    ) {}

    public function __invoke(array $data, UploadedFile $image): Dashboard
    {
        return DB::transaction(function () use ($data, $image) {
            // ビジネスロジック: ファイルサイズチェック
            if ($image->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('画像は5MB以下にしてください');
            }

            // Serviceで技術的処理(__invokeを呼び出し)
            $imageData = ($this->uploadImageService)($image, 'dashboards');

            $data['image_url'] = $imageData['url'];
            return $this->repository->create($data);
        });
    }
}
```

### ❌ BAD: Serviceにビジネスロジック

```php
class ImageUploadService
{
    public function uploadImage(UploadedFile $file, string $directory): array
    {
        // ビジネスロジックがServiceに混入（NG）
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('画像は5MB以下にしてください');
        }

        $filename = $this->generateFilename($file);
        $path = Storage::disk('s3')->putFileAs($directory, $file, $filename);
        return ['path' => $path, 'url' => Storage::disk('s3')->url($path)];
    }
}
```

---

## Serviceの使い分け

### 機能固有のServiceを使う場合

- **特定ドメインに関連する処理**: ダッシュボード複製、デバイスステータス同期など
- **複雑なビジネスロジックを含む**: ドメイン知識が必要な処理
- **その機能でのみ使用**: 他の機能では再利用しない

**例:**
```
app/Services/Dashboard/DashboardDuplicationService.php
app/Services/Device/StatusSyncService.php
app/Services/Karte/ExportService.php
```

### 共通/汎用的なServiceを使う場合

- **技術的な処理**: 画像アップロード、PDF生成、メール送信
- **ドメイン非依存**: どの機能からも利用可能
- **再利用性が高い**: 複数の機能で使用される

**例:**
```
app/Services/ImageUploadService.php
app/Services/PdfGenerationService.php
app/Services/EmailNotificationService.php
```

---

## テスト例

### 機能固有Serviceのユニットテスト

```php
<?php

namespace Tests\Unit\Services\Dashboard;

use App\Models\Dashboard;
use App\Services\Dashboard\PrepareDuplicateDataService;
use Tests\TestCase;

class PrepareDuplicateDataServiceTest extends TestCase
{
    private PrepareDuplicateDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PrepareDuplicateDataService();
    }

    /**
     * @test
     */
    public function 複製データが正しく準備される(): void
    {
        $dashboard = Dashboard::factory()->make([
            'name' => 'オリジナル',
            'layout' => 'grid',
            'is_public' => true,
        ]);

        // __invokeを呼び出し
        $result = ($this->service)($dashboard);

        $this->assertEquals('オリジナル (コピー)', $result['name']);
        $this->assertEquals('grid', $result['layout']);
        $this->assertFalse($result['is_public']); // 複製時は非公開
    }

    /**
     * @test
     */
    public function オーバーライドが適用される(): void
    {
        $dashboard = Dashboard::factory()->make(['name' => 'オリジナル']);

        // __invokeを呼び出し
        $result = ($this->service)($dashboard, [
            'name' => 'カスタム名',
            'is_public' => true,
        ]);

        $this->assertEquals('カスタム名', $result['name']);
        $this->assertTrue($result['is_public']);
    }
}
```

### 共通Serviceのユニットテスト

```php
<?php

namespace Tests\Unit\Services;

use App\Services\StatisticsCalculationService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class StatisticsCalculationServiceTest extends TestCase
{
    private StatisticsCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticsCalculationService();
    }

    /**
     * @test
     */
    public function 平均値を正しく計算できる(): void
    {
        $values = collect([10, 20, 30, 40, 50]);

        $average = $this->service->calculateAverage($values);

        $this->assertEquals(30.0, $average);
    }

    /**
     * @test
     */
    public function 空のコレクションで0を返す(): void
    {
        $values = collect([]);

        $average = $this->service->calculateAverage($values);

        $this->assertEquals(0, $average);
    }

    /**
     * @test
     */
    public function 中央値を正しく計算できる(): void
    {
        $values = collect([10, 20, 30, 40, 50]);

        $median = $this->service->calculateMedian($values);

        $this->assertEquals(30, $median);
    }

    /**
     * @test
     */
    public function 偶数個の値の中央値を計算できる(): void
    {
        $values = collect([10, 20, 30, 40]);

        $median = $this->service->calculateMedian($values);

        $this->assertEquals(25.0, $median);
    }

    /**
     * @test
     */
    public function 成長率を正しく計算できる(): void
    {
        $growthRate = $this->service->calculateGrowthRate(150, 100);

        $this->assertEquals(50.0, $growthRate);
    }

    /**
     * @test
     */
    public function 前期が0の場合は0を返す(): void
    {
        $growthRate = $this->service->calculateGrowthRate(100, 0);

        $this->assertEquals(0, $growthRate);
    }
}
```

### 外部API連携Serviceのモックテスト

```php
<?php

namespace Tests\Unit\Services;

use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalApiServiceTest extends TestCase
{
    private ExternalApiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExternalApiService();
    }

    /**
     * @test
     */
    public function ユーザー情報を正しく取得できる(): void
    {
        // HTTPレスポンスをモック
        Http::fake([
            '*/users/123' => Http::response([
                'id' => 123,
                'name' => 'Test User',
                'email' => 'test@example.com',
            ], 200),
        ]);

        $result = $this->service->fetchUser(123);

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('Test User', $result['name']);
    }

    /**
     * @test
     */
    public function API呼び出し失敗時に例外が発生する(): void
    {
        Http::fake([
            '*/users/999' => Http::response([], 404),
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $this->service->fetchUser(999);
    }

    /**
     * @test
     */
    public function リトライが機能する(): void
    {
        Http::fake([
            '*/users/123' => Http::sequence()
                ->push([], 500) // 1回目: エラー
                ->push([], 500) // 2回目: エラー
                ->push(['id' => 123, 'name' => 'Test User'], 200), // 3回目: 成功
        ]);

        $result = $this->service->fetchUser(123);

        $this->assertEquals(123, $result['id']);
    }
}
```

---

## チェックリスト

- [ ] **シングルアクション(`__invoke`のみ)になっているか**
- [ ] **1クラス1処理の原則に従っているか**
- [ ] 命名規則に従っているか（機能固有 or 共通/汎用）
- [ ] 適切なディレクトリに配置されているか
- [ ] 外部APIのエラーハンドリングが適切に実装されているか
- [ ] ログ出力が適切に行われているか
- [ ] タイムアウト設定がされているか（外部API連携の場合）
- [ ] リトライロジックが必要な場合は実装されているか
- [ ] 環境変数から設定を読み込んでいるか
- [ ] 例外処理が適切に実装されているか
- [ ] PHPDocで型定義がされているか
- [ ] テストが書かれているか
- [ ] レート制限が考慮されているか（外部API連携の場合）
- [ ] 再利用可能な設計になっているか
- [ ] ビジネスロジックの制御はUseCaseに委譲されているか
- [ ] 直接的なDB操作を行っていないか
