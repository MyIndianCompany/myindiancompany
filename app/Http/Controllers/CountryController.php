<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Country::query()
            ->orderBy('name')
            ->get();
        return CountryResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            Country::create([
                'name' => $request->input('name'),
                'country_code' => $request->input('country_code'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The country has been successfully created.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to create the country.',
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
    public function show(Country $country)
    {
        return new CountryResource($country);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        try {
            DB::beginTransaction();
            $country->update([
                'name' => $request->input('name'),
                'country_code' => $request->input('country_code'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The country has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the country.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        try {
            DB::beginTransaction();
            $country->delete();
            DB::commit();
            return response()->json([
                'message' => 'The country has been successfully deleted.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to delete the country.',
                'error' => $exception
            ], 401);
        }
    }
}
