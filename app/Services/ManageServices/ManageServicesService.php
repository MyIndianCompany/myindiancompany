<?php

namespace App\Services\ManageServices;

use App\Models\Service\ServiceCategoryFile;
use App\Models\Service\ServiceFile;

class ManageServicesService
{
    public function retrieveCategoryFiles()
    {
        $query = ServiceCategoryFile::select('original_file_name', 'file','mime_type')
            ->orderBy('created_at', 'desc')
            ->get();
        return $query;
    }

    public function retrieveServiceFiles()
    {
        $query = ServiceFile::select('original_file_name', 'file','mime_type')
            ->orderBy('created_at', 'desc')
            ->get();
        return $query;
    }
}
