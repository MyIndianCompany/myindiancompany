<?php

namespace App\Imports\Service;

use App\Models\Service\Service;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceVariant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class ServiceImport implements ToModel, WithBatchInserts, WithChunkReading, ShouldQueue, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        $serviceCategories = ServiceCategory::firstOrNew([
            'name' => trim($row['Category Name']),
        ]);
        if (!$serviceCategories->exists) {
            $serviceCategories->fill([
                'description' => trim($row['Category Description'] ?? null),
            ])->save();
        }
        $service = Service::firstOrNew([
            'name' => trim($row['Service Name']),
        ]);
        if (!$service->exists) {
            $service->fill([
                'description'  => trim($row['Service Description'] ?? null),
                'price'        => trim($row['Service Price'] ?? null)
            ])->save();
        }
        $serviceCategories->services()->syncWithoutDetaching([$service->id]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
