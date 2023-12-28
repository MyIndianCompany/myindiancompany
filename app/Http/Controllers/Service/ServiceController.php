<?php

namespace App\Http\Controllers\Service;

use App\Common\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceResource;
use App\Imports\Service\ServiceImport;
use App\Models\Service\Service;
use App\Models\Service\ServiceFile;
use App\Services\ManageServices\ManageServicesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Service::query()
            ->with(['files','variants'])
            ->orderBy('name')
            ->get();
        return ServiceResource::collection($query);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $service = Service::create([
                'name'         => $request->input('name'),
                'service_code' => $request->input('service_code'),
                'description'  => $request->input('description'),
                'price'        => $request->input('price'),
                'slug'         => $request->input('slug'),
                'remark'       => $request->input('remark')
            ]);

            $categories = $request->input('category_id');
            $service->categories()->attach($categories);
            $files =  $request->file('files');
            if($files) {
                foreach ($files as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_FILE_PATH, $originalFileName, 's3');
                    $fileUrl = Storage::disk('s3')->url($fileName);
                    $serviceFile = [
                        'service_id'         => $service->id,
                        'original_file_name' => $originalFileName,
                        'file'               => $fileUrl,
                        'mime_type'          => $file->getMimeType()
                    ];
                    ServiceFile::create($serviceFile);
                }
            }

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
            $uploadedFiles = $request->file('files');
            $filesToDelete = $request->input('files_to_delete');

            DB::beginTransaction();
            $service->update([
                'name'         => $request->has('name') ? $request->input('name') : $service->name,
                'service_code' => $request->has('service_code') ? $request->input('service_code') : $service->service_code,
                'description'  => $request->has('description') ? $request->input('description') : $service->description,
                'price'        => $request->has('price') ? $request->input('price') : $service->price,
                'slug'         => $request->has('slug') ? $request->input('slug') : $service->slug,
                'remark'       => $request->has('remark') ? $request->input('remark') : $service->remark
            ]);
            $categories = $request->input('category_id');
            $service->categories()->attach($categories);
            if ($filesToDelete) {
                foreach ($filesToDelete as $fileId) {
                    $fileToDelete = ServiceFile::where('service_id', $service->id)
                        ->where('id', $fileId)
                        ->first();

                    if ($fileToDelete) {
                        Storage::delete($fileToDelete->file);
                        $fileToDelete->delete();
                    }
                }
            }

            if($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_FILE_PATH, $originalFileName, 's3');
                    $fileUrl = Storage::disk('s3')->url($fileName);
                    $serviceFile = [
                        'service_id' => $service->id,
                        'original_file_name' => $originalFileName,
                        'file' => $fileUrl,
                        'mime_type' => $file->getMimeType(),
                    ];
                    ServiceFile::create($serviceFile);
                }
            }

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
    public function destroy(Service $service)
    {
        try {
            DB::beginTransaction();
            $service->delete();
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

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new ServiceImport)->queue($request->file('file'));
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

    public function serviceFiles()
    {
        $obj = new ManageServicesService();
        return $obj->retrieveServiceFiles();
    }

    public function getServiceVariants(Service $service)
    {
        $serviceVariants = $service->variants()->get();
        return ServiceResource::collection($serviceVariants);
    }
}
