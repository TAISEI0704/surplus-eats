<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ユーザープロフィール更新リクエスト
 */
final class UpdateProfileRequest extends FormRequest
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
        /** @var \App\Models\User $user */
        $user = $this->user();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
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
        ];
    }
}
