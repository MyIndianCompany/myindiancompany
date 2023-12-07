<?php

namespace App\Http\Controllers\Agent;

use App\Exception\CustomException\MicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\AgentRequest;
use App\Http\Resources\Agent\AgentResource;
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
    public function show(Agent $agent)
    {
        $getAgent = Agent::join('agent_contact', 'agents.id', '=', 'agent_contact.agent_id')
            ->join('contacts', 'agent_contact.contact_id', '=', 'contacts.id')
            ->join('cities', 'contacts.city_id', '=', 'cities.id')
            ->join('districts', 'contacts.district_id', '=', 'districts.id')
            ->join('states', 'contacts.state_id', '=', 'states.id')
            ->join('service_categories', 'agents.service_category_id', '=', 'service_categories.id')
            ->select(
                'agents.name as name',
                'service_categories.name as job_title',
                'contacts.phone as phone',
                'contacts.alt_phone as alt_phone',
                'contacts.email as email',
                'contacts.address as address',
                'contacts.landmark as landmark',
                'cities.name as city',
                'districts.name as district',
                'states.name as state',
                'contacts.zip_code as zip_code',
                'agents.photo as photo'
            )
            ->where('agents.id', $agent->id)
            ->first();

        if (!$getAgent) {
            throw new MicException('Agent not found');
        }

//        $city = $getAgent->city ?? '';
//        $district = $getAgent->district ?? '';
//        $state = $getAgent->state ?? '';
//
//        $address = implode(', ', array_filter([$city, $district, $state]));
//        $getAgent->address = $address;

        return new AgentResource($getAgent);
    }

//    public function showed(Request $request, Agent $agent)
//    {
//        try {
//            $agentRelation = Agent::with([
//                'serviceCategory',
//                'contacts.country',
//                'contacts.state',
//                'contacts.district',
//                'contacts.city',
//            ])->find($agent->id);
//
//            if (!$agentRelation) {
//                return response()->json(['message' => 'Agent not found'], 404);
//            }
//
//            return response()->json(['agent' => $agentRelation], 200);
//        } catch (\Exception $err) {
//            report($err);
//            return response()->json(['message' => 'Error Occurred', 'error' => $err], 500);
//        }
//    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgentRequest $request, Agent $agent)
    {
        try {
            $city = City::with('district.state.country')
                  ->find($request->validated('city_id'));
            DB::beginTransaction();
            $agent->update($request->validated());
            $agent->contacts()->update([
                'country_id'  => $city->district->state->country->id,
                'state_id'    => $city->district->state->id,
                'district_id' => $city->district->id,
                'city_id'     => $city->id
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Account updated successfully.'
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            report($err);
            return response()->json([
                'message' => 'Failed to update account!',
                'error' => $err->getMessage()
            ], 500);
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
