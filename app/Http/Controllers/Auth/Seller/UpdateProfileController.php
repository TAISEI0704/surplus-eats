<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\UpdateProfileRequest;
use App\Http\Resources\Auth\SellerResource;
use App\UseCases\Auth\Seller\UpdateProfileUseCase;
use Illuminate\Http\JsonResponse;

/**
 * 販売者プロフィール更新Controller
 */
final class UpdateProfileController extends Controller
{
    public function __construct(
        private readonly UpdateProfileUseCase $useCase
    ) {}

    /**
     * プロフィール更新処理
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function __invoke(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = $request->user();

        $data = $request->validated();

        // 画像ファイルがある場合は追加
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $updated = ($this->useCase)($seller, $data);

        return response()->json([
            'seller' => new SellerResource($updated),
            'message' => 'プロフィールを更新しました。',
        ], 200);
    }
}
