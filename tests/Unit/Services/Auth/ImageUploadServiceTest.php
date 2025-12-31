<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * ImageUploadService Unit Test
 */
class ImageUploadServiceTest extends TestCase
{
    private ImageUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImageUploadService();
        Storage::fake('public');
    }

    /**
     * 画像保存のテスト
     */
    public function test_store(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $filename = $this->service->store($file, 'images');

        $this->assertEquals('test.jpg', $filename);
        Storage::disk('public')->assertExists('images/test.jpg');
    }

    /**
     * 画像削除のテスト
     */
    public function test_delete(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');
        $this->service->store($file, 'images');

        $result = $this->service->delete('test.jpg', 'images');

        $this->assertTrue($result);
        Storage::disk('public')->assertMissing('images/test.jpg');
    }

    /**
     * 画像URL取得のテスト
     */
    public function test_url(): void
    {
        $url = $this->service->url('test.jpg', 'images');

        $this->assertStringContainsString('images/test.jpg', $url);
    }
}
