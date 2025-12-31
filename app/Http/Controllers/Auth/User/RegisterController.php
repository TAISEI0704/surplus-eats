<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\UseCases\Auth\User\RegisterUseCase;
use Illuminate\Http\JsonResponse;

/**
 * ユーザー登録Controller
 */
final class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterUseCase $useCase
    ) {}

    /**
     * ユーザー登録処理
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $result = ($this->useCase)($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }
}
