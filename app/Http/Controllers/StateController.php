<?php

namespace App\Http\Controllers;

use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = State::query()
            ->orderBy('name')
            ->get();
        return StateResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            State::create([
                'country_id' => $request->input('country_id'),
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
    public function show(State $state)
    {
        return new StateResource($state);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        try {
            DB::beginTransaction();
            $state->update([
                'country_id' => $request->input('country_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            DB::commit();
            return response()->json([
                'message' => 'The state has been successfully updated.'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'We encountered an issue while attempting to update the state.',
                'error' => $exception
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        try {
            DB::beginTransaction();
            $state->delete();
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
