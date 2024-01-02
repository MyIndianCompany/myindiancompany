<?php

namespace App\Http\Controllers\Agent;

use App\Common\Constants\Constants;
use App\Exception\CustomException\MicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\AgentRequest;
use App\Http\Resources\Agent\AgentResource;
use App\Models\Agent\Agent;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
    public function show()
    {
        $user = auth()->user()->id;
        $getAgent = Agent::where('user_id', $user)
            ->leftJoin('agent_contact', 'agents.id', '=', 'agent_contact.agent_id')
            ->leftJoin('contacts', 'agent_contact.contact_id', '=', 'contacts.id')
            ->leftJoin('cities', 'contacts.city_id', '=', 'cities.id')
            ->leftJoin('districts', 'contacts.district_id', '=', 'districts.id')
            ->leftJoin('states', 'contacts.state_id', '=', 'states.id')
            ->leftJoin('service_categories', 'agents.service_category_id', '=', 'service_categories.id')
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
                'agents.pan_card_docs as pan_card_docs',
                'agents.photo as profile_picture'
            )
            ->first();

        if (!$getAgent) {
            throw new MicException('Agent not found');
        }
        return new AgentResource($getAgent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgentRequest $request)
    {
        try {
            $user = auth()->user()->id;
            $agent = Agent::where('user_id', $user)->first();

            if (!$agent) {
                throw new \Exception('Agent not found for the authenticated user.');
            }

            $city = City::with('district.state.country')
                  ->find($request->validated('city_id'));

            if (!$city) {
                throw new \Exception('City not found for the provided name.');
            }

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

    public function updateDocument(Request $request)
    {
        try {
            $user = auth()->user()->id;
            $agent = Agent::where('user_id', $user)->first();
            if (!$agent) {
                throw new \Exception('Agent not found for the authenticated user.');
            }
            $uploadedFile = $request->file('pan_card_docs');
            if (!$uploadedFile) {
                throw new MicException('File not found!');
            }
            DB::beginTransaction();
            $originalFileName = $uploadedFile->getClientOriginalName();
            $fileName = $uploadedFile->storeAs(Constants::AGENT_PAN_CARD, $originalFileName, 's3');
            $fileUrl = Storage::disk('s3')->url($fileName);
            $agent->pan_card_docs = $fileUrl;
            $agent->save();
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

    public function profilePictureUpload(Request $request)
    {
        try {
            $user = auth()->user()->id;
            $agent = Agent::where('user_id', $user)->first();
            if (!$agent) {
                throw new \Exception('Agent not found for the authenticated user.');
            }
            $uploadedFile = $request->file('profile_picture');
            if (!$uploadedFile) {
                throw new MicException('File not found!');
            }
            DB::beginTransaction();
            $originalFileName = $uploadedFile->getClientOriginalName();
            $fileName = $uploadedFile->storeAs(Constants::AGENT_PROFILE_PICTURE, $originalFileName, 's3');
            $fileUrl = Storage::disk('s3')->url($fileName);
            $agent->photo = $fileUrl;
            $agent->save();
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
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        //
    }
}
