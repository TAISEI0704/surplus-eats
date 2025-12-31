<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\UpdateProfileRequest;
use App\Http\Resources\Auth\UserResource;
use App\UseCases\Auth\User\UpdateProfileUseCase;
use Illuminate\Http\JsonResponse;

/**
 * ユーザープロフィール更新Controller
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        $updated = ($this->useCase)($user, $request->validated());

        return response()->json([
            'user' => new UserResource($updated),
            'message' => 'プロフィールを更新しました。',
        ], 200);
    }
}
