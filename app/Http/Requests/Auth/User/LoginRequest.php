<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ユーザーログインリクエスト
 */
final class LoginRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
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
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
        ];
    }
}
