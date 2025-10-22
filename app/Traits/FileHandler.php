<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait FileHandler
{
    public function uploadFile($file, $folder)
    {
        if (!$file) {
            return null;
        }

        // Define upload path
        $uploadPath = public_path($folder);

        // Create directory if not exists
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Generate unique file name
        $fileName = time() . uniqid() . '_' . $file->getClientOriginalName();

        // Move file to folder
        $file->move($uploadPath, $fileName);

        // Return relative path (to store in DB)
        return "{$folder}/{$fileName}";
    }

    public function deleteFile($filePath): void
    {
        if ($filePath) {
            $fullPath = public_path($filePath);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}