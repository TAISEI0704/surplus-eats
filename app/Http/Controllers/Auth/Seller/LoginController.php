<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\LoginRequest;
use App\Http\Resources\Auth\SellerResource;
use App\UseCases\Auth\Seller\LoginUseCase;
use Illuminate\Http\JsonResponse;

/**
 * 販売者ログインController
 */
final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginUseCase $useCase
    ) {}

    /**
     * ログイン処理
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $result = ($this->useCase)($request->validated());

        return response()->json([
            'seller' => new SellerResource($result['seller']),
            'token' => $result['token'],
        ], 200);
    }
}
