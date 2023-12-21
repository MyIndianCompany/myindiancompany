<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerEnquiryRequest;
use App\Http\Resources\CustomerEnquiryResource;
use App\Models\CustomerEnquiry;
use App\Models\Service\Service;
use App\Models\Service\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerEnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = CustomerEnquiry::join('services', 'customer_enquiries.service', '=', 'services.id')
            ->join('service_variants', 'customer_enquiries.service_variant', '=', 'service_variants.id')
            ->select(
                'customer_enquiries.name as name',
                'customer_enquiries.phone as phone',
                'customer_enquiries.email as email',
                'customer_enquiries.message as message',
                'services.name as service_name',
                'service_variants.name as service_variant_name',
            )
            ->orderBy('customer_enquiries.created_at', 'desc')
            ->get();

        return CustomerEnquiryResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CustomerEnquiryRequest $request)
    {
        try {
            $serviceVariantId = $request->input('service_variant');
            $service = ServiceVariant::findOrFail($serviceVariantId)->service_id;
            DB::beginTransaction();
            CustomerEnquiry::create([
                'service' => $service,
                'service_variant' => $request->input('service_variant'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
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


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerEnquiry $customerEnquiry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerEnquiry $customerEnquiry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerEnquiry $customerEnquiry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerEnquiry $customerEnquiry)
    {
        //
    }
}
