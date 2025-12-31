---
applyTo: "app/Support/**/*.php,tests/Unit/Support/**/*.php"
---
# Support Layer Rules

Support層はアプリケーション全体で使用されるヘルパー関数やユーティリティクラスを提供します。

---

## 🎯 基本方針

### Support層の役割

本プロジェクトでは**Support層**を使用してアプリケーション横断的な機能を提供します：

- **ヘルパー関数**: 繰り返し使用される小さな処理
- **ユーティリティクラス**: 共通的な処理をまとめたクラス
- **フォーマッター**: データ整形処理
- **バリデーター**: カスタムバリデーションロジック
- **変換処理**: データ型変換や形式変換

---

## 責務

### ✅ Supportが行うべきこと

- 文字列操作（フォーマット、変換）
- 日付・時刻操作（フォーマット、計算）
- 配列操作（フィルタリング、変換）
- ファイル操作ヘルパー
- 数値計算・変換
- 汎用的なバリデーション
- データ整形・変換

### ❌ Supportが行ってはいけないこと

- ビジネスロジック（UseCaseの責務）
- データベース操作（Query/Repositoryの責務）
- 外部API呼び出し（Infrastructureの責務）
- HTTPリクエスト処理（Controllerの責務）
- ドメイン固有の処理（Modelの責務）

---

## 命名規則

### ディレクトリ構造

```
app/Support/
├── Helpers/              # ヘルパー関数群
│   ├── StringHelper.php
│   ├── DateHelper.php
│   └── ArrayHelper.php
├── Formatters/           # フォーマッター
│   ├── DateFormatter.php
│   ├── NumberFormatter.php
│   └── PhoneFormatter.php
├── Validators/           # カスタムバリデーター
│   ├── PhoneValidator.php
│   └── PostalCodeValidator.php
└── Converters/           # データ変換
    ├── CsvConverter.php
    └── JsonConverter.php
```

---

## 基本実装パターン

### 文字列ヘルパー

```php
<?php

namespace App\Support\Helpers;

class StringHelper
{
    /**
     * 文字列を指定文字数で切り詰める
     *
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $text, int $length, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * スネークケースをキャメルケースに変換
     *
     * @param string $value
     * @return string
     */
    public static function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace('_', '', ucwords($value, '_')));
    }

    /**
     * キャメルケースをスネークケースに変換
     *
     * @param string $value
     * @return string
     */
    public static function camelToSnake(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    /**
     * ランダムな文字列を生成
     *
     * @param int $length
     * @return string
     */
    public static function random(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * マスク処理（メールアドレスなど）
     *
     * @param string $email
     * @return string
     */
    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);

        return $maskedName . '@' . $domain;
    }
}
```

### 日付ヘルパー

```php
<?php

namespace App\Support\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * 日本語の曜日を取得
     *
     * @param Carbon $date
     * @return string
     */
    public static function getJapaneseDayOfWeek(Carbon $date): string
    {
        $days = ['日', '月', '火', '水', '木', '金', '土'];
        return $days[$date->dayOfWeek];
    }

    /**
     * 営業日かどうかを判定
     *
     * @param Carbon $date
     * @return bool
     */
    public static function isBusinessDay(Carbon $date): bool
    {
        // 土日を除外
        if ($date->isWeekend()) {
            return false;
        }

        // 祝日判定（簡易版）
        // 実際には祝日マスタと照合する必要がある
        return true;
    }

    /**
     * 次の営業日を取得
     *
     * @param Carbon $date
     * @return Carbon
     */
    public static function getNextBusinessDay(Carbon $date): Carbon
    {
        $nextDay = $date->copy()->addDay();

        while (!self::isBusinessDay($nextDay)) {
            $nextDay->addDay();
        }

        return $nextDay;
    }

    /**
     * 経過時間を人間が読みやすい形式で取得
     *
     * @param Carbon $date
     * @return string
     */
    public static function diffForHumans(Carbon $date): string
    {
        $now = Carbon::now();
        $diffInMinutes = $now->diffInMinutes($date);

        if ($diffInMinutes < 1) {
            return 'たった今';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . '分前';
        } elseif ($diffInMinutes < 1440) {
            return floor($diffInMinutes / 60) . '時間前';
        } else {
            return floor($diffInMinutes / 1440) . '日前';
        }
    }

    /**
     * 年齢を計算
     *
     * @param Carbon $birthDate
     * @return int
     */
    public static function calculateAge(Carbon $birthDate): int
    {
        return $birthDate->diffInYears(Carbon::now());
    }
}
```

### 配列ヘルパー

```php
<?php

namespace App\Support\Helpers;

class ArrayHelper
{
    /**
     * 配列を指定キーでグループ化
     *
     * @param array<int, array<string, mixed>> $array
     * @param string $key
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!isset($item[$key])) {
                continue;
            }

            $groupKey = $item[$key];
            if (!isset($result[$groupKey])) {
                $result[$groupKey] = [];
            }

            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * 配列から指定キーの値のみを抽出
     *
     * @param array<int, array<string, mixed>> $array
     * @param string $key
     * @return array<int, mixed>
     */
    public static function pluck(array $array, string $key): array
    {
        return array_map(fn($item) => $item[$key] ?? null, $array);
    }

    /**
     * 配列をフラット化
     *
     * @param array<int|string, mixed> $array
     * @return array<int, mixed>
     */
    public static function flatten(array $array): array
    {
        $result = [];

        array_walk_recursive($array, function ($value) use (&$result) {
            $result[] = $value;
        });

        return $result;
    }

    /**
     * 配列の特定キーで検索
     *
     * @param array<int, array<string, mixed>> $array
     * @param string $key
     * @param mixed $value
     * @return array<string, mixed>|null
     */
    public static function findByKey(array $array, string $key, mixed $value): ?array
    {
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] === $value) {
                return $item;
            }
        }

        return null;
    }
}
```

### 数値フォーマッター

```php
<?php

namespace App\Support\Formatters;

class NumberFormatter
{
    /**
     * 数値を3桁区切りでフォーマット
     *
     * @param int|float $number
     * @param int $decimals
     * @return string
     */
    public static function format(int|float $number, int $decimals = 0): string
    {
        return number_format($number, $decimals);
    }

    /**
     * 金額を日本円形式でフォーマット
     *
     * @param int|float $amount
     * @return string
     */
    public static function formatCurrency(int|float $amount): string
    {
        return '¥' . number_format($amount);
    }

    /**
     * パーセンテージをフォーマット
     *
     * @param float $value
     * @param int $decimals
     * @return string
     */
    public static function formatPercentage(float $value, int $decimals = 1): string
    {
        return number_format($value * 100, $decimals) . '%';
    }

    /**
     * ファイルサイズを人間が読みやすい形式に変換
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function formatFileSize(int $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $decimals) . ' ' . $units[$i];
    }
}
```

### 電話番号フォーマッター

```php
<?php

namespace App\Support\Formatters;

class PhoneFormatter
{
    /**
     * 電話番号をハイフン区切りにフォーマット
     *
     * @param string $phone
     * @return string
     */
    public static function format(string $phone): string
    {
        // ハイフンを削除
        $phone = str_replace(['-', '−', '‐'], '', $phone);

        // 11桁の携帯電話番号
        if (strlen($phone) === 11 && preg_match('/^(070|080|090)/', $phone)) {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7, 4);
        }

        // 10桁の固定電話番号
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
        }

        return $phone;
    }

    /**
     * 電話番号のハイフンを削除
     *
     * @param string $phone
     * @return string
     */
    public static function normalize(string $phone): string
    {
        return str_replace(['-', '−', '‐'], '', $phone);
    }
}
```

### カスタムバリデーター

```php
<?php

namespace App\Support\Validators;

class PhoneValidator
{
    /**
     * 日本の電話番号として有効かチェック
     *
     * @param string $phone
     * @return bool
     */
    public static function isValid(string $phone): bool
    {
        // ハイフンを削除
        $phone = str_replace(['-', '−', '‐'], '', $phone);

        // 携帯電話番号（11桁、070/080/090で始まる）
        if (preg_match('/^(070|080|090)[0-9]{8}$/', $phone)) {
            return true;
        }

        // 固定電話番号（10桁）
        if (preg_match('/^0[1-9][0-9]{8}$/', $phone)) {
            return true;
        }

        return false;
    }
}
```

```php
<?php

namespace App\Support\Validators;

class PostalCodeValidator
{
    /**
     * 郵便番号として有効かチェック
     *
     * @param string $postalCode
     * @return bool
     */
    public static function isValid(string $postalCode): bool
    {
        // ハイフンあり・なし両方に対応
        return preg_match('/^\d{3}-?\d{4}$/', $postalCode) === 1;
    }

    /**
     * 郵便番号を正規化（ハイフンなしに統一）
     *
     * @param string $postalCode
     * @return string
     */
    public static function normalize(string $postalCode): string
    {
        return str_replace('-', '', $postalCode);
    }
}
```

---

## グローバルヘルパー関数

`app/Support/helpers.php` にグローバルヘルパー関数を定義できます：

```php
<?php

use App\Support\Helpers\StringHelper;
use App\Support\Helpers\DateHelper;

if (!function_exists('truncate')) {
    function truncate(string $text, int $length, string $suffix = '...'): string
    {
        return StringHelper::truncate($text, $length, $suffix);
    }
}

if (!function_exists('japanese_day_of_week')) {
    function japanese_day_of_week(Carbon\Carbon $date): string
    {
        return DateHelper::getJapaneseDayOfWeek($date);
    }
}

if (!function_exists('format_currency')) {
    function format_currency(int|float $amount): string
    {
        return App\Support\Formatters\NumberFormatter::formatCurrency($amount);
    }
}
```

`composer.json` に登録：

```json
{
    "autoload": {
        "files": [
            "app/Support/helpers.php"
        ]
    }
}
```

---

## チェックリスト

- [ ] 命名規約に従っているか
- [ ] アプリケーション横断的な機能か（特定機能に依存していないか）
- [ ] ビジネスロジックを含んでいないか
- [ ] 適切な名前空間に配置されているか
- [ ] static メソッドとして実装されているか（状態を持たない）
- [ ] PHPDocで型定義がされているか
- [ ] エッジケースを考慮しているか
- [ ] テストが書かれているか
- [ ] 既存のLaravelヘルパー関数と重複していないか
