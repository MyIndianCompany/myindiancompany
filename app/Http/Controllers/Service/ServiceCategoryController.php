<?php

namespace App\Http\Controllers\Service;

use App\Common\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Service\ServiceCategoryResource;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceCategoryFile;
use App\Services\ManageServices\ManageServicesService;
use App\Utility\FileUploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ServiceCategory::query()
            ->where('type', '=', 'available')
            ->with('files')
            ->orderBy('name')
            ->get();
        return ServiceCategoryResource::collection($query);
    }

    public function getUpcomingServices()
    {
        $query = ServiceCategory::query()
            ->where('type', '=', 'upcoming services')
            ->with('files')
            ->orderBy('name')
            ->get();
        return ServiceCategoryResource::collection($query);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $files =  $request->file('files');
            DB::beginTransaction();
            $serviceCategory = ServiceCategory::create([
                'service_category_id' => $request->input('service_category_id'),
                'name'                => $request->input('name'),
                'description'         => $request->input('description'),
                'slug'                => $request->input('slug'),
                'type'                => $request->input('type'),
                'remark'              => $request->input('remark')
            ]);
            if($files) {
                foreach ($files as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_CATEGORY_FILE_PATH, $originalFileName, 's3');
                    $fileUrl = Storage::disk('s3')->url($fileName);
                    $serviceCategoryFile = [
                        'category_id'        => $serviceCategory->id,
                        'original_file_name' => $originalFileName,
                        'file'               => $fileUrl,
                        'mime_type'          => $file->getMimeType(),
                    ];
                    ServiceCategoryFile::create($serviceCategoryFile);
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
    public function show(ServiceCategory $serviceCategory)
    {
        return new ServiceCategoryResource($serviceCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        try {
            $uploadedFiles = $request->file('files');
            $filesToDelete = $request->input('files_to_delete');

            DB::beginTransaction();

            $serviceCategory->update([
                'service_category_id' => $request->has('service_category_id') ? $request->input('service_category_id') : $serviceCategory->service_category_id,
                'name'                => $request->has('name') ? $request->input('name') : $serviceCategory->name,
                'description'         => $request->has('description') ? $request->input('description') : $serviceCategory->description,
                'slug'                => $request->has('slug') ? $request->input('slug') : $serviceCategory->slug,
                'type'                => $request->has('type') ? $request->input('type') : $serviceCategory->type,
                'remark'              => $request->has('remark') ? $request->input('remark') : $serviceCategory->remark,
            ]);

            // Handle files to delete
            if ($filesToDelete) {
                foreach ($filesToDelete as $fileId) {
                    $fileToDelete = ServiceCategoryFile::where('category_id', $serviceCategory->id)
                        ->where('id', $fileId)
                        ->first();

                    if ($fileToDelete) {
                        Storage::delete($fileToDelete->file);
                        $fileToDelete->delete();
                    }
                }
            }

            // Handle uploaded files
            if($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_CATEGORY_FILE_PATH, $originalFileName, 's3');
                    $fileUrl = Storage::disk('s3')->url($fileName);
                    $serviceCategoryFile = [
                        'category_id' => $serviceCategory->id,
                        'original_file_name' => $originalFileName,
                        'file' => $fileUrl,
                        'mime_type' => $file->getMimeType(),
                    ];
                    ServiceCategoryFile::create($serviceCategoryFile);
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
    public function destroy(ServiceCategory $serviceCategory)
    {
        try {
            DB::beginTransaction();
            $serviceCategory->delete();
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

    public function getServices(ServiceCategory $serviceCategory)
    {
        $services = $serviceCategory->services()->with(['files', 'categories' => function ($query) {
            $query->select('name');
        }])->get();
        return ServiceCategoryResource::collection($services);
    }

//    public function categoryFiles()
//    {
//        $obj = new ManageServicesService();
//        return $obj->retrieveCategoryFiles();
//    }

    public function categoryFiles(ServiceCategory $serviceCategory)
    {
        $query = DB::table('services')
            ->join('service_service_category', 'services.id', '=', 'service_service_category.service_id')
            ->join('service_categories', 'service_service_category.service_category_id', '=', 'service_categories.id')
            ->join('service_files', 'services.id', '=', 'service_files.service_id')
            ->select(
                'service_categories.id as service_category_id',
                'service_categories.name as service_category_name',
                'services.id as service_id',
                'services.name as service_name',
                'services.service_code as service_code',
                'services.description as service_description',
                'services.price as service_price',
                'service_files.original_file_name as original_file_name',
                'service_files.file as file',
                'service_files.mime_type as mime_type'
            )
            ->where('service_categories.id', $serviceCategory->id)
            ->whereNull('service_categories.deleted_at')
            ->whereNull('services.deleted_at')
            ->whereNull('service_files.deleted_at')
            ->orderBy('services.created_at', 'desc')
            ->get();

        return $query;
    }


}
