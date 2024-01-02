<?php

namespace App\Exports\Customer;

use App\Models\CustomerEnquiry;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerEnquiryExport implements
    FromCollection,
    Responsable,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    use Exportable;
    private $fileName = 'customer_enquiries.xlsx';
    private $writerType = Excel::XLSX;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];
    /**
    * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function collection()
    {
        return CustomerEnquiry::with(['services', 'serviceVariant'])->get();
    }

    /**
     * @param CustomerEnquiry $customerEnquiry
     */
    public function map($customerEnquiry): array
    {
        return [
            optional($customerEnquiry->services)->name,
            optional($customerEnquiry->serviceVariant)->name,
            $customerEnquiry->name,
            $customerEnquiry->phone,
            $customerEnquiry->email,
            $customerEnquiry->address,
            $customerEnquiry->message,
        ];
    }

    public function headings(): array
    {
        return [
            'Service',
            'Service Variant',
            'Name',
            'Phone',
            'Email',
            'Address',
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
