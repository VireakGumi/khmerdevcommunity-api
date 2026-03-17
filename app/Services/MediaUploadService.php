<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    public function storeImages(array $files, string $directory = 'feed'): array
    {
        return collect($files)
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->map(fn (UploadedFile $file) => $this->storeImage($file, $directory))
            ->values()
            ->all();
    }

    public function storeImage(UploadedFile $file, string $directory = 'feed'): string
    {
        $path = $this->storeOptimizedImage($file, $directory);

        return Storage::disk('public')->url($path);
    }

    private function storeOptimizedImage(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $directory.'/'.Str::uuid().'.'.$extension;

        if (! function_exists('imagecreatetruecolor')) {
            return $file->storePublicly($directory, 'public');
        }

        $mime = $file->getMimeType() ?: '';
        $image = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };

        if (! $image) {
            return $file->storePublicly($directory, 'public');
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 1800;

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($height / $width) * $newWidth);
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if (in_array($mime, ['image/png', 'image/webp'], true)) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        ob_start();

        match ($mime) {
            'image/png' => imagepng($image, null, 7),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, null, 82) : imagejpeg($image, null, 82),
            default => imagejpeg($image, null, 82),
        };

        $content = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($filename, $content);

        return $filename;
    }
}
