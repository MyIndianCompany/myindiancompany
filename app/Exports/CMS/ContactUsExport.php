<?php

namespace App\Exports\CMS;

use App\Models\CMS\ContactUs;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactUsExport implements
    FromCollection,
    Responsable,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    use Exportable;
    private $fileName = 'contactUs.xlsx';
    private $writerType = Excel::XLSX;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ContactUs::select('name', 'phone', 'email', 'message')->get();
    }
    /**
     * @param ContactUs $contactUs
     */
    public function map($contactUs): array
    {
        return [
            $contactUs->name,
            $contactUs->phone,
            $contactUs->email,
            $contactUs->message,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Email',
            'Message'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // TODO: Implement styles() method.
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
