<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;

class FileStorageService
{
    public static function storeFile(UploadedFile $file, $filePath, $fileName, $isPublic=false){
        if ($isPublic){ // Check Local storage with public access
            $file->storeAs($filePath, $fileName,'public'); // For 'public' disk
        }else{
            $file->storeAs($filePath, $fileName);    // For 'local' and 'S3' disk
        }

    }
}
