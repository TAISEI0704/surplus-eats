<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * 画像アップロードサービス（Seller用）
 */
final class ImageUploadService
{
    /**
     * 画像を保存
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string 保存されたファイルパス
     */
    public function store(UploadedFile $file, string $directory = 'images'): string
    {
        $filename = $file->getClientOriginalName();
        $file->storeAs($directory, $filename, 'public');

        return $filename;
    }

    /**
     * 画像を削除
     *
     * @param string $filename
     * @param string $directory
     * @return bool
     */
    public function delete(string $filename, string $directory = 'images'): bool
    {
        return Storage::disk('public')->delete("{$directory}/{$filename}");
    }

    /**
     * 画像のURLを取得
     *
     * @param string $filename
     * @param string $directory
     * @return string
     */
    public function url(string $filename, string $directory = 'images'): string
    {
        return Storage::url("{$directory}/{$filename}");
    }
}
