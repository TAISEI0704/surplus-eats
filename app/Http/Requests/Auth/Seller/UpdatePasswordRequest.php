<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * 販売者パスワード変更リクエスト
 */
final class UpdatePasswordRequest extends FormRequest
{
    /**
     * 認可判定
     */
    public function authorize(): bool
    {
        return true; // auth:sanctum middleware で認証済み
    }

    /**
     * バリデーションルール
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', Password::default(), 'confirmed'],
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
            'current_password.required' => '現在のパスワードは必須です。',
            'new_password.required' => '新しいパスワードは必須です。',
            'new_password.confirmed' => '新しいパスワード確認が一致しません。',
        ];
    }
}
