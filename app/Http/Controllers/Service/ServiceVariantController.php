<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceVariantResource;
use App\Models\Service\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ServiceVariant::query()
            ->orderBy('name')
            ->get();
        return ServiceVariantResource::collection($query);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            ServiceVariant::create([
                'service_id' => $request->input('service_id'),
                'name' => $request->input('name'),
                'service_variant_code' => $request->input('service_variant_code'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
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
    public function show(ServiceVariant $serviceVariant)
    {
        return new ServiceVariantResource($serviceVariant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceVariant $serviceVariant)
    {
        try {
            DB::beginTransaction();
            $serviceVariant->update([
                'service_id' => $request->input('service_id'),
                'name' => $request->input('name'),
                'service_variant_code' => $request->input('service_variant_code'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The service has been successfully created.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the service.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceVariant $serviceVariant)
    {
        try {
            DB::beginTransaction();
            $serviceVariant->delete();
            DB::commit();
            return response()->json([
                'message' => 'The service has been successfully created.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the service.',
                'error' => $exception
            ], 401);
        }
    }
}
