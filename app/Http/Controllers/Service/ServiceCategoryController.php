<?php

namespace App\Http\Controllers\Service;

use App\Common\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategory\ServiceCategoryRequest;
use App\Http\Resources\Service\ServiceCategoryResource;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceCategoryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $serviceCategory = ServiceCategory::select('id', 'name')->orderBy('name')->get();
        return  ServiceCategoryResource::collection($serviceCategory);
    }

    public function getAvailableServices(): AnonymousResourceCollection
    {
        $query = ServiceCategory::query()
            ->where('type', '=', 'available')
            ->with(['files' => function ($query) {
                $query->where('type', '=', Constants::THUMBNAIL)->where('status', '=', Constants::STATUS_ACTIVE);
            }])
            ->orderBy('name')
            ->get();
        return ServiceCategoryResource::collection($query);
    }

    public function getUpcomingServices(): AnonymousResourceCollection
    {
        $query = ServiceCategory::query()
            ->where('type', '=', 'upcoming services')
            ->with(['files' => function ($query) {
                $query->where('type', '=', Constants::THUMBNAIL)->where('status', '=', Constants::STATUS_ACTIVE);
            }])
            ->orderBy('name')
            ->get();
        return ServiceCategoryResource::collection($query);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(ServiceCategoryRequest $request): JsonResponse
    {
        try {
            $files =  $request->file('files');

            $originalName = $request->input('name');
            $slugBase = Str::slug($originalName, '-', 'hi', ['&' => 'and']);
            $slug = $slugBase;
            $slugExists = ServiceCategory::where('slug', $slug)->exists();
            $counter = 1;

            while ($slugExists) {
                $slug = $slugBase . '-' . $counter;
                $slugExists = ServiceCategory::where('slug', $slug)->exists();
                $counter++;
            }

            DB::beginTransaction();
            $serviceCategory = ServiceCategory::create([
                'service_category_id' => $request->input('service_category_id'),
                'name'                => $originalName,
                'description'         => $request->input('description'),
                'slug'                => $slug,
                'type'                => $request->input('type'),
                'remark'              => $request->input('remark')
            ]);
            if($files) {
                foreach ($files as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_CATEGORY_FILE_PATH, $originalFileName, 'public');
                    $fileUrl = Storage::disk('public')->url($fileName);
                    $serviceCategoryFile = [
                        'category_id'        => $serviceCategory->id,
                        'original_file_name' => $originalFileName,
                        'file'               => $fileUrl,
                        'mime_type'          => $file->getMimeType(),
                        'type'               => Constants::THUMBNAIL,
                        'status'             => Constants::STATUS_ACTIVE
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
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory)
    {
        return new ServiceCategoryResource($serviceCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory): JsonResponse
    {
        try {
            $uploadedFiles = $request->file('files');
            $filesToDelete = $request->input('files_to_delete');

            $originalName = $request->has('name') ? $request->input('name') : $serviceCategory->name;
            $existingSlug = $serviceCategory->slug ?? null;
            $slugBase = Str::slug($originalName, '-', 'hi', ['@' => 'at', '&' => 'and']);
            $slug = $slugBase;
            $slugExists = ServiceCategory::where('slug', $slug)->where('slug', '!=', $existingSlug)->exists();
            $counter = 1;

            while ($slugExists) {
                $slug = $slugBase . '-' . $counter;
                $slugExists = ServiceCategory::where('slug', $slug)->where('slug', '!=', $existingSlug)->exists();
                $counter++;
            }

            DB::beginTransaction();

            $serviceCategory->update([
                'service_category_id' => $request->has('service_category_id') ? $request->input('service_category_id') : $serviceCategory->service_category_id,
                'name'                => $originalName,
                'description'         => $request->has('description') ? $request->input('description') : $serviceCategory->description,
                'slug'                => $slug,
                'type'                => $request->has('type') ? $request->input('type') : $serviceCategory->type,
                'remark'              => $request->has('remark') ? $request->input('remark') : $serviceCategory->remark,
            ]);

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

            if($uploadedFiles) {
                foreach ($uploadedFiles as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_CATEGORY_FILE_PATH, $originalFileName, 'public');
                    $fileUrl = Storage::disk('public')->url($fileName);
                    $serviceCategoryFile = [
                        'category_id'        => $serviceCategory->id,
                        'original_file_name' => $originalFileName,
                        'file'               => $fileUrl,
                        'mime_type'          => $file->getMimeType(),
                        'type'               => Constants::THUMBNAIL,
                        'status'             => Constants::STATUS_ACTIVE
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
    public function destroy(ServiceCategory $serviceCategory): JsonResponse
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

    public function getServices($slug): AnonymousResourceCollection
    {
        $serviceCategory = ServiceCategory::where('slug', $slug)->firstOrFail();
        $services = $serviceCategory->services()
            ->with(['files','variants', 'categories' => function ($query) {
                $query->select('name');
            }])
            ->get();
        return ServiceCategoryResource::collection($services);
    }

    public function categoryFiles(ServiceCategory $serviceCategory): Collection
    {
        return DB::table('services')
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
    }

    public function slider(Request $request): JsonResponse
    {
        try {
            $serviceCategoryId = $request->input('service_category_id');
            $serviceCategory = $serviceCategoryId !== null ? ServiceCategory::find($serviceCategoryId)->id : null;
            $files = $request->file('files');
            DB::beginTransaction();
            if ($files) {
                foreach ($files as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $file->storeAs(Constants::SERVICE_CATEGORY_SLIDER_FILE_PATH, $originalFileName, 'public');
                    $fileUrl = Storage::disk('public')->url($fileName);
                    $serviceCategoryFile = [
                        'category_id'        => $serviceCategory,
                        'original_file_name' => $originalFileName,
                        'file'               => $fileUrl,
                        'mime_type'          => $file->getMimeType(),
                        'type'               => Constants::SLIDER,
                        'status'             => Constants::STATUS_ACTIVE
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

    public function getSlider($slug): AnonymousResourceCollection
    {
        $serviceCategory = ServiceCategory::where('slug', $slug)->firstOrFail();
        $query = $serviceCategory->files()
            ->where('type', Constants::SLIDER)
            ->where('status', Constants::STATUS_ACTIVE)
            ->get();
        return ServiceCategoryResource::collection($query);
    }
}
