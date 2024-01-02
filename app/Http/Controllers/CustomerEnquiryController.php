<?php

namespace App\Http\Controllers;

use App\Exports\Customer\CustomerEnquiryExport;
use App\Http\Requests\Customer\CustomerEnquiryRequest;
use App\Http\Resources\CustomerEnquiryResource;
use App\Models\CustomerEnquiry;
use App\Models\Service\Service;
use App\Models\Service\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CustomerEnquiryController extends Controller
{
    public function index()
    {
        $query = CustomerEnquiry::leftJoin('services', 'customer_enquiries.service', '=', 'services.id')
            ->leftJoin('service_variants', 'customer_enquiries.service_variant', '=', 'service_variants.id')
            ->select(
                'customer_enquiries.name as name',
                'customer_enquiries.phone as phone',
                'customer_enquiries.email as email',
                'customer_enquiries.address as address',
                'customer_enquiries.message as message',
                'services.name as service_name',
                'service_variants.name as service_variant_name',
            )
            ->orderBy('customer_enquiries.created_at', 'desc')
            ->get();
        return CustomerEnquiryResource::collection($query);
    }

    public function create(CustomerEnquiryRequest $request)
    {
        try {
            $serviceVariantId = $request->input('service_variant');
            $service = $serviceVariantId !== null ? ServiceVariant::find($serviceVariantId)->service_id : null;
            DB::beginTransaction();
            CustomerEnquiry::create([
                'service' => $request->has('service') ? $request->input('service') : $service,
                'service_variant' => $serviceVariantId,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'message' => $request->input('message')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Task completed.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'Oops! Something went wrong. Please try again later.',
                'error' => $exception->getMessage()
            ], 401);
        }
    }

    public function export()
    {
        return new CustomerEnquiryExport();
    }
}
