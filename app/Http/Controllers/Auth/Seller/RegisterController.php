<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\RegisterRequest;
use App\Http\Resources\Auth\SellerResource;
use App\UseCases\Auth\Seller\RegisterUseCase;
use Illuminate\Http\JsonResponse;

/**
 * 販売者登録Controller
 */
final class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterUseCase $useCase
    ) {}

    /**
     * 販売者登録処理
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // 画像ファイルがある場合は追加
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $result = ($this->useCase)($data);

        return response()->json([
            'seller' => new SellerResource($result['seller']),
            'token' => $result['token'],
        ], 201);
    }
}
