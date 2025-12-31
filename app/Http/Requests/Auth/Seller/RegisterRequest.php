<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * 販売者登録リクエスト
 */
final class RegisterRequest extends FormRequest
{
    /**
     * 認可判定
     */
    public function authorize(): bool
    {
        return true; // 公開エンドポイント
    }

    /**
     * バリデーションルール
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:sellers,email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
            'device_name' => ['nullable', 'string', 'max:255'],
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
            'name.required' => '店舗名は必須です。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'password.required' => 'パスワードは必須です。',
            'password.confirmed' => 'パスワード確認が一致しません。',
            'phone.required' => '電話番号は必須です。',
            'address.required' => '住所は必須です。',
            'content.required' => '店舗説明は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.max' => '画像サイズは2MB以下にしてください。',
            'image.mimes' => '画像はjpeg、png、jpg形式のみ対応しています。',
        ];
    }
}
