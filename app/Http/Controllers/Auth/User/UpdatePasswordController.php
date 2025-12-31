<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\UpdatePasswordRequest;
use App\UseCases\Auth\User\UpdatePasswordUseCase;
use Illuminate\Http\JsonResponse;

/**
 * ユーザーパスワード変更Controller
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        ($this->useCase)($user, $request->validated());

        return response()->json([
            'message' => 'パスワードを変更しました。',
        ], 200);
    }
}
