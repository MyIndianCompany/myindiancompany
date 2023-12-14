<?php

namespace App\Utility;

use App\Services\Storage\FileStorageService;

class FileUploadHelper
{
    public static function  uploadFile($file,$pathToStore){
        try {
            // Get filename with the extension
            $fileNameWithExt = $file->getClientOriginalName();

            // Get just filenamef
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            // Get file extension
            $extension = $file->getClientOriginalExtension();

            // Filename to Store
            $fileNameToStore = $filename . time() . '.' . $extension;

            // Upload File
            FileStorageService::storeFile($file . $pathToStore, $fileNameToStore);

            return $pathToStore . $fileNameToStore;

        }catch (\Exception $error){
            report($error);
            return response()->json([
                'message' => "Unable to upload the file.",
            ], 503);
        }
    }
}
