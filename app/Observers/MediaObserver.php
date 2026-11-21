<?php

namespace App\Observers;

use App\Models\Media;

class MediaObserver
{
    public function creating(Media $media): void
    {
        // File name-i WebP-yə dəyişdir (yüklənmədən əvvəl)
        if ($this->shouldConvert($media)) {
            $originalFileName = pathinfo($media->file_name, PATHINFO_FILENAME);
            $media->file_name = $originalFileName . '.webp';
        }
    }

    public function created(Media $media): void
    {
        // Dosyayı WebP formatına çevir və yaddaşaya yaz
        app()->make(\App\Listeners\ConvertMediaToWebp::class)->handle($media);
    }

    protected function shouldConvert(Media $media): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff'];
        $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }
}
