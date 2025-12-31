<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ユーザー登録リクエスト
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
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
            'name.required' => '名前は必須です。',
            'name.max' => '名前は255文字以内で入力してください。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'password.required' => 'パスワードは必須です。',
            'password.confirmed' => 'パスワード確認が一致しません。',
        ];
    }
}
