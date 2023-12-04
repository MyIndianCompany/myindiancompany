<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceResource;
use App\Models\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->limit ?: 50;
        $query = Service::query()->with(['categories','variants']);
        $paginate = $query->paginate($limit);
        return ServiceResource::collection($paginate);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $service = Service::create([
                'name' => $request->input('name'),
                'service_code' => $request->input('service_code'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
            ]);

            $categories = $request->input('category_id');
            $service->categories()->attach($categories);

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        try {
            DB::beginTransaction();
            $service->update([
                'name' => $request->input('name'),
                'service_code' => $request->input('service_code'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'slug' => $request->input('slug'),
                'remark' => $request->input('remark')
            ]);
            $categories = $request->input('category_id');
            $service->categories()->sync($categories);
            DB::commit();
            return response()->json([
                'message' => 'The service has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the service.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            DB::beginTransaction();
            $service->delete();
            DB::commit();
            return response()->json([
                'message' => 'The service has been successfully deleted.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to delete the service.',
                'error' => $exception
            ], 401);
        }
    }
}
