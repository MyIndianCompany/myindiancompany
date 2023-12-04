<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceTagResource;
use App\Models\Service\ServiceTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->limit ?: 50;
        $query = ServiceTag::query();
        $paginate = $query->paginate($limit);
        return ServiceTagResource::collection($paginate);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            ServiceTag::create([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The service tag has been successfully created.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the service tag.',
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
    public function show(ServiceTag $serviceTag)
    {
        return new ServiceTagResource($serviceTag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceTag $serviceTag)
    {
        try {
            DB::beginTransaction();
            $serviceTag->update([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The service tag has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the service tag.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceTag $serviceTag)
    {
        try {
            DB::beginTransaction();
            $serviceTag->delete();
            DB::commit();
            return response()->json([
                'message' => 'The service tag has been successfully deleted.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to delete the service tag.',
                'error' => $exception
            ], 401);
        }
    }
}
