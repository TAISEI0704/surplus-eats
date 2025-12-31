<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Seller\UpdatePasswordRequest;
use App\UseCases\Auth\Seller\UpdatePasswordUseCase;
use Illuminate\Http\JsonResponse;

/**
 * 販売者パスワード変更Controller
 */
final class UpdatePasswordController extends Controller
{
    public function __construct(
        private readonly UpdatePasswordUseCase $useCase
    ) {}

    /**
     * パスワード変更処理
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = $request->user();

        ($this->useCase)($seller, $request->validated());

        return response()->json([
            'message' => 'パスワードを変更しました。',
        ], 200);
    }
}
