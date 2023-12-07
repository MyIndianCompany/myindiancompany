<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceCategoryResource;
use App\Models\Service\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ServiceCategory::query()
            ->orderBy('name')
            ->get();
        return ServiceCategoryResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            ServiceCategory::create([
                'service_category_id' => $request->input('service_category_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The service category has been successfully created.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the service category.',
                'error' => $exception
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
    public function show(ServiceCategory $serviceCategory)
    {
        return new ServiceCategoryResource($serviceCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        try {
            DB::beginTransaction();
            $serviceCategory->update([
                'service_category_id' => $request->input('service_category_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The service category has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the service category.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        try {
            DB::beginTransaction();
            $serviceCategory->delete();
            DB::commit();
            return response()->json([
                'message' => 'The service category has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the service category.',
                'error' => $exception
            ], 401);
        }
    }
}
