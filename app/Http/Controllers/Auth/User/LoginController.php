<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\LoginRequest;
use App\Http\Resources\Auth\UserResource;
use App\UseCases\Auth\User\LoginUseCase;
use Illuminate\Http\JsonResponse;

/**
 * ユーザーログインController
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
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 200);
    }
}
