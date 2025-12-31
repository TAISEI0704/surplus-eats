<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\PasswordHashService;
use Tests\TestCase;

/**
 * PasswordHashService Unit Test
 */
class PasswordHashServiceTest extends TestCase
{
    private PasswordHashService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PasswordHashService();
    }

    /**
     * パスワードハッシュ化のテスト
     */
    public function test_hash(): void
    {
        $password = 'password123';

        $hashed = $this->service->hash($password);

        $this->assertIsString($hashed);
        $this->assertNotEquals($password, $hashed);
        $this->assertStringStartsWith('$2y$', $hashed);
    }

    /**
     * パスワード検証のテスト（正しいパスワード）
     */
    public function test_check_with_correct_password(): void
    {
        $password = 'password123';
        $hashed = $this->service->hash($password);

        $result = $this->service->check($password, $hashed);

        $this->assertTrue($result);
    }

    /**
     * パスワード検証のテスト（間違ったパスワード）
     */
    public function test_check_with_incorrect_password(): void
    {
        $password = 'password123';
        $hashed = $this->service->hash($password);

        $result = $this->service->check('wrong-password', $hashed);

        $this->assertFalse($result);
    }
}
