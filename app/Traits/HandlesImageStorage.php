<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HandlesImageStorage
{
    /**
     * Store an image file and return its path
     *
     * @param \Illuminate\Http\UploadedFile|null $imageFile
     * @param string $directory
     * @return string|null
     */
    protected function storeImage($imageFile, string $directory = 'posts'): ?string
    {
        if (!$imageFile) {
            return null;
        }

        return $imageFile->store($directory, 'public');
    }

    /**
     * Delete an image file if it exists
     *
     * @param string|null $imagePath
     * @return void
     */
    protected function deleteImage(?string $imagePath): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }
} 