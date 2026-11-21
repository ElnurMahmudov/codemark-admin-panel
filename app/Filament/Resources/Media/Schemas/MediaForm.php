<?php

namespace App\Filament\Resources\Media\Schemas;

use Awcodes\Curator\Resources\Media\Schemas\MediaForm as CuratorMediaForm;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Components\Forms\Uploader;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Form;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaForm extends CuratorMediaForm
{
    public static function form(Form $form): Form
    {
        return parent::form($form);
    }

    public static function getUploaderField(): Uploader
    {
        $uploader = parent::getUploaderField();

        $uploader->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?array {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (\Exception $e) {
                return null;
            }

            $filename = $component->shouldPreserveFilenames()
                ? (string) str($file->getClientOriginalName())->beforeLast('.')
                : (string) str()->uuid();

            $extension = strtolower($file->getClientOriginalExtension());
            $directory = $component->getDirectory();
            $disk = $component->getDiskName();

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'], true)) {
                $filename = $component->shouldPreserveFilenames()
                    ? (string) str($file->getClientOriginalName())->beforeLast('.')
                    : (string) str()->uuid();

                if (Storage::disk($disk)->exists(trim($directory . '/' . $filename . '.webp', '/'))) {
                    $filename = $filename . '-' . time();
                }

                $webpPath = trim($directory . '/' . $filename . '.webp', '/');
                $content = self::buildWebPContent($file->getRealPath(), $extension);

                if ($content === null) {
                    return null;
                }

                Storage::disk($disk)->put($webpPath, $content);
                $size = strlen($content);
                $imageData = getimagesize($file->getRealPath());
                $width = $imageData[0] ?? null;
                $height = $imageData[1] ?? null;

                return [
                    'disk' => $disk,
                    'directory' => $directory,
                    'visibility' => $component->getVisibility(),
                    'name' => $filename,
                    'path' => $webpPath,
                    'exif' => null,
                    'width' => $width,
                    'height' => $height,
                    'size' => $size,
                    'type' => 'image/webp',
                    'ext' => 'webp',
                ];
            }

            // Fallback to original upload behavior for non-image files
            return call_user_func(
                $component->getSaveUploadedFileUsing(),
                $component,
                $file,
            );
        });

        return $uploader;
    }

    protected static function buildWebPContent(string $path, string $extension): ?string
    {
        $extension = strtolower($extension);

        $create = match ($extension) {
            'jpg', 'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',
            'gif' => 'imagecreatefromgif',
            'bmp' => 'imagecreatefrombmp',
            default => null,
        };

        if (! $create || ! function_exists($create)) {
            return null;
        }

        $image = @$create($path);
        if (! $image) {
            return null;
        }

        $tempPath = sys_get_temp_dir() . '/' . uniqid('curator-webp-') . '.webp';
        if (! imagewebp($image, $tempPath, 80)) {
            imagedestroy($image);
            return null;
        }

        imagedestroy($image);
        $content = file_get_contents($tempPath);
        @unlink($tempPath);

        return $content;
    }
}
