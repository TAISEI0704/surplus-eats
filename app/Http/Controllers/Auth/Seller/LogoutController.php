<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\Seller\LogoutUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 販売者ログアウトController
 */
final class LogoutController extends Controller
{
    public function __construct(
        private readonly LogoutUseCase $useCase
    ) {}

    /**
     * ログアウト処理
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = $request->user();

        ($this->useCase)($seller);

        return response()->json([
            'message' => 'ログアウトしました。',
        ], 200);
    }
}
