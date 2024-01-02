<?php

namespace App\Http\Controllers\CMS;

use App\Exports\CMS\ContactUsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\ContactUsRequest;
use App\Models\CMS\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{
    public function store(ContactUsRequest $request)
    {
        try {
            DB::beginTransaction();
            ContactUs::create([
                'name'    => $request->input('name'),
                'phone'   => $request->input('phone'),
                'email'   => $request->input('email'),
                'message' => $request->input('message'),
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

    public function export(ContactUs $contactUs)
    {
        return new ContactUsExport();
    }
}
