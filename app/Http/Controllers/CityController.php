<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//    public function index()
//    {
//        $limit = request()->limit ?: 50;
//        $query = City::query()->with('district.state.country')
//            ->when(request()->has('search'), function ($query) {
//                $searchTerm = request()->input('search');
//                $query->where('name', 'like', '%' . $searchTerm . '%');
//            })
//            ->orderBy('name');
//
//        $paginate = $query->paginate($limit);
//        return CityResource::collection($paginate);
//    }

    public function index()
    {
        $query = City::query()
            ->select('id', 'name', 'description')
            ->when(request()->has('search'), function ($query) {
                $searchTerm = request()->input('search');
                return $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('name')
            ->get();;
        return CityResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            City::create([
                'district_id' => $request->input('district_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description')
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
    public function show(City $city)
    {
        return new CityResource($city);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        try {
            DB::beginTransaction();
            $city->update([
                'district_id' => $request->input('district_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The city has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the updated.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        try {
            DB::beginTransaction();
            $city->delete();
            DB::commit();
            return response()->json([
                'message' => 'The city has been successfully deleted.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to delete the city.',
                'error' => $exception
            ], 401);
        }
    }
}
