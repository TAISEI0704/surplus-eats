<?php

declare(strict_types=1);

namespace App\UseCases\Auth\User;

use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\TokenService;
use Illuminate\Support\Facades\DB;

/**
 * ユーザー登録UseCase
 */
final class RegisterUseCase
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService
    ) {}

    /**
     * ユーザー登録処理を実行
     *
     * @param array{name: string, email: string, password: string, device_name?: string} $data
     * @return array{user: User, token: string}
     */
    public function __invoke(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // ユーザー作成
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            // トークン生成
            $deviceName = $data['device_name'] ?? 'web';
            $token = $this->tokenService->createToken($user, $deviceName);

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
