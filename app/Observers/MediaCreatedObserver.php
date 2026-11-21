<?php

namespace App\Observers;

use App\Models\Media;

class MediaCreatedObserver
{
    public function created(Media $media): void
    {
        // Skip əgər extension WebP-dir
        if ($media->ext === 'webp') {
            return;
        }

        try {
            $this->convertToWebp($media);
        } catch (\Exception $e) {
            \Log::warning("Media WebP conversion failed: {$e->getMessage()}");
        }
    }

    public function deleting(Media $media): void
    {
        // Faylı disk-dən sil
        $this->deleteFile($media);
    }

    protected function deleteFile(Media $media): void
    {
        $basePath = storage_path('app/public');
        $fullPath = $basePath . '/' . $media->path;

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            \Log::info("Media file deleted: {$media->path}");
        }
    }

    protected function convertToWebp(Media $media): void
    {
        // Curator'un public disk'i storage/app/public-ə yazır
        $basePath = storage_path('app/public');
        $originalFullPath = $basePath . '/' . $media->path;

        \Log::info("Converting media to WebP", [
            'path' => $media->path,
            'full_path' => $originalFullPath,
        ]);

        // Check if file exists
        if (!file_exists($originalFullPath)) {
            \Log::warning("Media file not found: {$originalFullPath}");
            return;
        }

        // Determine image type based on extension
        $imageType = match ($media->ext) {
            'jpg', 'jpeg' => 'jpeg',
            'png' => 'png',
            'gif' => 'gif',
            default => null,
        };

        if (!$imageType) {
            \Log::warning("Unsupported image type: {$media->ext}");
            return;
        }

        // Load image using GD
        $createFunction = "imagecreatefrom{$imageType}";
        $image = $createFunction($originalFullPath);

        if (!$image) {
            \Log::warning("Failed to load image: {$originalFullPath}");
            return;
        }

        // Generate WebP path
        $webpPath = preg_replace('/\.' . preg_quote($media->ext, '/') . '$/i', '.webp', $media->path);
        $webpFullPath = $basePath . '/' . $webpPath;

        // Ensure directory exists
        $webpDir = dirname($webpFullPath);
        if (!is_dir($webpDir)) {
            mkdir($webpDir, 0755, true);
        }

        // Save as WebP
        if (!imagewebp($image, $webpFullPath, 80)) {
            \Log::warning("Failed to save WebP: {$webpFullPath}");
            imagedestroy($image);
            return;
        }

        imagedestroy($image);

        // Delete original file
        @unlink($originalFullPath);

        // Update database
        Media::where('id', $media->id)->update([
            'path' => $webpPath,
            'ext' => 'webp',
            'type' => 'image/webp',
        ]);

        \Log::info("Media successfully converted to WebP", [
            'new_path' => $webpPath,
        ]);
    }
}
