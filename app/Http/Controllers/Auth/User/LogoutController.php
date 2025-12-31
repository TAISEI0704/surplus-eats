<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\User\LogoutUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ユーザーログアウトController
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        ($this->useCase)($user);

        return response()->json([
            'message' => 'ログアウトしました。',
        ], 200);
    }
}
