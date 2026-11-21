<?php

namespace App\Listeners;

use App\Models\Media;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GdDriver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ConvertMediaToWebp
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new GdDriver());
    }

    public function handle(Media $media): void
    {
        // Skip əgər faylı WebP deyilsə
        if (Str::lower(pathinfo($media->file_name, PATHINFO_EXTENSION)) !== 'webp') {
            return;
        }

        try {
            $this->convertToWebp($media);
        } catch (\Exception $e) {
            // Xəta logla amma yükləmə sırası qırılmasın
            \Log::warning("WebP çevrilməsi uğursuz oldu media ID {$media->id}: {$e->getMessage()}");
        }
    }

    protected function convertToWebp(Media $media): void
    {
        $disk = Storage::disk($media->disk ?? config('curator.default_disk', 'public'));
        
        // Curator'un gətirdiyi file path
        $uploadedFilePath = null;
        
        // Temp dirə baxaq - Curator burada yükləyir
        $searchPaths = [
            $media->directory . '/' . pathinfo($media->file_name, PATHINFO_FILENAME) . '.*',
            storage_path('app/' . $media->directory) . '/' . pathinfo($media->file_name, PATHINFO_FILENAME) . '.*',
        ];

        // Orijinal faylı tap (hər hansı formatda)
        foreach ($searchPaths as $pattern) {
            $files = glob(storage_path('app/' . $pattern));
            if (!empty($files)) {
                $uploadedFilePath = $files[0];
                break;
            }
        }

        // Disk-də direkt bax
        if (!$uploadedFilePath) {
            $baseName = pathinfo($media->file_name, PATHINFO_FILENAME);
            $dirPath = storage_path('app/' . $media->directory);
            
            if (is_dir($dirPath)) {
                foreach (glob($dirPath . '/' . $baseName . '.*') as $file) {
                    $uploadedFilePath = $file;
                    break;
                }
            }
        }

        if (!$uploadedFilePath || !file_exists($uploadedFilePath)) {
            return;
        }

        // Orijinal faylı oku
        $imageContent = file_get_contents($uploadedFilePath);
        $image = $this->imageManager->read($imageContent);

        // WebP-yə çevir
        $webpContent = $image->toWebp(quality: 80);

        // WebP faylını yaddaşaya yaz (lazım olan yola)
        $webpPath = storage_path('app/' . $media->directory . '/' . $media->file_name);
        
        // Direktoriya yaradılmadısa yarat
        $webpDir = dirname($webpPath);
        if (!is_dir($webpDir)) {
            mkdir($webpDir, 0755, true);
        }

        // WebP faylını yaz
        file_put_contents($webpPath, $webpContent);

        // Orijinal faylı sil
        if (file_exists($uploadedFilePath) && $uploadedFilePath !== $webpPath) {
            unlink($uploadedFilePath);
        }
    }
}
