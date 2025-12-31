---
applyTo: "app/Http/Requests/**/*.php,tests/Unit/Http/Requests/**/*.php"
---
# Request Layer Rules (FormRequest)

FormRequestはバリデーションとリクエストデータの検証を担当し、Controllerに渡る前にデータの妥当性を保証します。

---

## 責務

### ✅ FormRequestが行うべきこと

- リクエストデータのバリデーション
- 認可チェック（authorize）
- カスタムエラーメッセージの定義
- バリデーション後のデータ加工
- カスタムバリデーションルールの適用

### ❌ FormRequestが行ってはいけないこと

- ビジネスロジック
- DB操作
- 外部API呼び出し
- 複雑なデータ変換

---

## 命名規則

```
app/Http/Requests/{Feature}/{Action}Request.php
```

**命名例:**
- `{Feature}`: 機能名（例: `Dashboard`, `Device`, `Karte`, `User`）
- `{Action}`: アクション（例: `Create`, `Update`, `Delete`）

### 具体例

```
app/Http/Requests/
├── Dashboard/
│   ├── CreateRequest.php        # ダッシュボード作成
│   ├── UpdateRequest.php        # ダッシュボード更新
│   └── DeleteRequest.php        # ダッシュボード削除（必要な場合）
├── Device/
│   ├── CreateRequest.php
│   ├── UpdateRequest.php
│   └── UpdateStatusRequest.php  # ステータス更新
└── User/
    ├── CreateRequest.php
    └── UpdateProfileRequest.php # プロフィール更新
```

---

## 基本実装パターン

### 基本的なFormRequest実装

```php
<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * リクエストの認可判定
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // 認証済みユーザーのみ許可
        return $this->user() !== null;

        // または特定の権限を持つユーザーのみ許可
        // return $this->user()->can('create', Dashboard::class);
    }

    /**
     * バリデーションルールの定義
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'layout' => ['required', 'string', 'in:grid,list,kanban'],
            'is_public' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }

    /**
     * カスタムエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ダッシュボード名は必須です',
            'name.max' => 'ダッシュボード名は255文字以内で入力してください',
            'layout.required' => 'レイアウトは必須です',
            'layout.in' => 'レイアウトは grid、list、kanban のいずれかを指定してください',
        ];
    }

    /**
     * バリデーション属性の表示名
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'ダッシュボード名',
            'description' => '説明',
            'layout' => 'レイアウト',
            'is_public' => '公開設定',
        ];
    }
}
```

### バリデーション前のデータ加工

```php
use Illuminate\Support\Str;

/**
 * バリデーション前のデータ準備
 *
 * @return void
 */
protected function prepareForValidation(): void
{
    $this->merge([
        'slug' => Str::slug($this->name),
        'is_active' => $this->boolean('is_active'),
    ]);
}
```

### 更新時の実装パターン

```php
<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $dashboardId = $this->route('id');
        // Policy を使用した認可チェック
        // return $this->user()->can('update', Dashboard::find($dashboardId));

        return $this->user() !== null;
    }

    public function rules(): array
    {
        $dashboardId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // 自分以外でユニークチェック
                Rule::unique('dashboards')->ignore($dashboardId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'layout' => ['required', 'string', 'in:grid,list,kanban'],
            'is_public' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ダッシュボード名は必須です',
            'name.unique' => 'このダッシュボード名は既に使用されています',
            'layout.required' => 'レイアウトは必須です',
            'layout.in' => 'レイアウトは grid、list、kanban のいずれかを指定してください',
        ];
    }
}
```

### 配列・ネストデータのバリデーション

```php
public function rules(): array
{
    return [
        'products' => ['required', 'array', 'min:1'],
        'products.*.name' => ['required', 'string', 'max:255'],
        'products.*.price' => ['required', 'numeric', 'min:0'],
        'products.*.tags' => ['array', 'max:5'],
        'products.*.tags.*' => ['string', 'distinct', 'max:50'],

        // ネストしたオブジェクト
        'author.name' => ['required', 'string'],
        'author.email' => ['required', 'email'],
    ];
}
```

### ファイルアップロードのバリデーション

```php
public function rules(): array
{
    return [
        'image' => [
            'required',
            'file',
            'image',
            'mimes:jpeg,png,jpg,webp',
            'max:2048', // 2MB
            'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
        ],
        'documents' => ['array', 'max:5'],
        'documents.*' => [
            'file',
            'mimes:pdf,doc,docx',
            'max:10240', // 10MB
        ],
    ];
}

public function messages(): array
{
    return [
        'image.required' => '画像は必須です',
        'image.image' => '画像ファイルをアップロードしてください',
        'image.mimes' => '画像形式はJPEG、PNG、JPG、WebPのみ対応しています',
        'image.max' => '画像サイズは2MB以下にしてください',
        'image.dimensions' => '画像サイズは100x100〜4000x4000の範囲で指定してください',
    ];
}
```

### 条件付きバリデーション

```php
public function rules(): array
{
    return [
        'payment_method' => ['required', 'in:credit_card,bank_transfer,cash'],
        'card_number' => [
            'required_if:payment_method,credit_card',
            'string',
            'size:16',
        ],
        'card_expiry' => [
            'required_if:payment_method,credit_card',
            'date_format:Y-m',
            'after:today',
        ],
        'bank_name' => [
            'required_if:payment_method,bank_transfer',
            'string',
        ],
    ];
}
```

### バリデーション後の追加チェック

```php
use Illuminate\Validation\Validator;

/**
 * バリデーション後の追加チェック
 *
 * @return array
 */
public function after(): array
{
    return [
        function (Validator $validator) {
            // 在庫数チェックなどのビジネスルール検証
            if ($this->quantity > $this->getAvailableStock()) {
                $validator->errors()->add(
                    'quantity',
                    '在庫数を超えています'
                );
            }

            // 複数フィールドの関連性チェック
            if ($this->start_date && $this->end_date) {
                if ($this->start_date >= $this->end_date) {
                    $validator->errors()->add(
                        'end_date',
                        '終了日は開始日より後の日付を指定してください'
                    );
                }
            }
        }
    ];
}

/**
 * 利用可能在庫数を取得
 *
 * @return int
 */
private function getAvailableStock(): int
{
    // リポジトリなどから取得
    return 100;
}
```

### バリデーション成功後の処理

```php
/**
 * バリデーション成功後の処理
 *
 * @return void
 */
protected function passedValidation(): void
{
    // データの正規化
    $this->merge([
        'slug' => Str::slug($this->name),
        'formatted_price' => number_format($this->price, 2),
    ]);
}
```

### 最初の失敗で停止

```php
/**
 * バリデーターが最初のルール失敗時に停止すべきかどうか
 *
 * @var bool
 */
protected $stopOnFirstFailure = true;
```

---

## よく使うバリデーションルール

### 基本

- `required`: 必須
- `nullable`: null許可
- `string`: 文字列
- `numeric`: 数値
- `integer`: 整数
- `boolean`: 真偽値
- `array`: 配列
- `date`: 日付
- `email`: メールアドレス
- `url`: URL
- `ip`: IPアドレス
- `json`: JSON文字列

### 文字列

- `min:3`: 最小文字数
- `max:255`: 最大文字数
- `size:10`: 固定文字数
- `alpha`: アルファベットのみ
- `alpha_num`: 英数字のみ
- `alpha_dash`: 英数字とハイフン・アンダースコアのみ

### 数値

- `min:0`: 最小値
- `max:100`: 最大値
- `between:0,100`: 範囲指定
- `digits:4`: 桁数固定
- `digits_between:2,4`: 桁数範囲

### 日付

- `before:tomorrow`: 指定日より前
- `after:today`: 指定日より後
- `date_format:Y-m-d`: 日付フォーマット

### データベース

- `exists:users,id`: レコード存在確認
- `unique:users,email`: ユニークチェック

### ファイル

- `file`: ファイル
- `image`: 画像ファイル
- `mimes:jpeg,png`: MIMEタイプ
- `max:2048`: 最大サイズ（KB）

### 条件付き

- `required_if:field,value`: 条件付き必須
- `required_with:field`: 他フィールドが存在する時必須
- `required_without:field`: 他フィールドが存在しない時必須

---

## チェックリスト

- [ ] 命名規約 `{Feature}/{Action}Request.php` に従っているか
- [ ] すべての必須項目にバリデーションが設定されているか
- [ ] 適切なバリデーションルールが選択されているか
- [ ] カスタムエラーメッセージが日本語で設定されているか
- [ ] 認可チェック（authorize）が適切に実装されているか
- [ ] ファイルアップロードのサイズ・形式チェックが適切か
- [ ] 配列・ネストデータのバリデーションが適切か
- [ ] ユニーク制約が適切に設定されているか（更新時は ignore を使用）
- [ ] バリデーション後の追加チェックが必要な場合、after()メソッドで実装されているか
- [ ] テストが書かれているか
