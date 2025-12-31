<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 販売者プロフィール更新リクエスト
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
        /** @var \App\Models\Seller $seller */
        $seller = $this->user();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('sellers', 'email')->ignore($seller->id),
            ],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'address' => ['sometimes', 'required', 'string', 'max:500'],
            'content' => ['sometimes', 'required', 'string', 'max:1000'],
            'image' => ['sometimes', 'nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
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
            'phone.required' => '電話番号は必須です。',
            'address.required' => '住所は必須です。',
            'content.required' => '店舗説明は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.max' => '画像サイズは2MB以下にしてください。',
        ];
    }
}
