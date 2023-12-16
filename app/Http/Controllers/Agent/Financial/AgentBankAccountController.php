<?php

namespace App\Http\Controllers\Agent\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\Financial\AgentBankAccountRequest;
use App\Http\Resources\Agent\Financial\AgentBankAccountResource;
use App\Models\Agent\AgentBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentBankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = AgentBankAccount::query()->get();
        return AgentBankAccountResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgentBankAccountRequest $request)
    {
        try {
            DB::beginTransaction();
            $agent = auth()->user()->id;
            AgentBankAccount::create([
                'agent_id'            => $agent,
                'account_holder_name' => $request->input('account_holder_name'),
                'bank_name'           => $request->input('bank_name'),
                'account_number'      => $request->input('account_number'),
                'type'                => $request->input('account_type'),
                'ifsc'                => $request->input('ifsc')
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
     * Display the specified resource.
     */
    public function show(AgentBankAccount $agentBankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgentBankAccount $agentBankAccount)
    {
        try {
            DB::beginTransaction();
            $agent = auth()->user()->id;
            $agentBankAccount->update([
                'agent_id'            => $agent,
                'account_holder_name' => $request->input('account_holder_name'),
                'bank_name'           => $request->input('bank_name'),
                'account_number'      => $request->input('account_number'),
                'type'                => $request->input('account_type'),
                'ifsc'                => $request->input('ifsc')
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
     * Remove the specified resource from storage.
     */
    public function destroy(AgentBankAccount $agentBankAccount)
    {
        try {
            DB::beginTransaction();
            $agentBankAccount->delete();
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
}
