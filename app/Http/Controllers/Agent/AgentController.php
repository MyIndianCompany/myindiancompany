<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent\Agent;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Request $request, Agent $agent)
    {
        try {
            $agentRelation = Agent::with([
                'serviceCategory',
                'contacts.country',
                'contacts.state',
                'contacts.district',
                'contacts.city',
            ])->find($agent->id);

            if (!$agentRelation) {
                return response()->json(['message' => 'Agent not found'], 404);
            }

            return response()->json(['agent' => $agentRelation], 200);
        } catch (\Exception $err) {
            report($err);
            return response()->json(['message' => 'Error Occurred', 'error' => $err], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        try {
            $cityId = $request->input('city_id');
            $city = City::with('district.state.country')->find($cityId);
            DB::beginTransaction();
            $agent->update([
                'service_category_id' => $request->input('service_category_id')
            ]);

            $agent->contacts()->update([
                'country_id' => $city->district->state->country->id,
                'state_id' => $city->district->state->id,
                'district_id' => $city->district->id,
                'city_id' => $city->id
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Your account details have been saved'
            ], 200);
        } catch (\Exception $err) {
            DB::rollBack();
            report($err);
            return response()->json([
                'message' => 'Error Occur',
                'error' => $err
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        //
    }
}
