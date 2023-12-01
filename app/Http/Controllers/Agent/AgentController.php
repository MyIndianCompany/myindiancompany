<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent\Agent;
use App\Models\Contact;
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
     * Show the form for creating a new resource.
     */
    public function create()
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        try {
            DB::beginTransaction();
            $agent->update([
                'job_title' => $request->input('job_title')
            ]);
            $agent->contacts()->update([
                'address' => $request->input('address')
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
