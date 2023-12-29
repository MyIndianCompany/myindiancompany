<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\Auth\AuthController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Service\ServiceCategoryController;
use App\Http\Controllers\Service\ServiceTagController;
use App\Http\Controllers\Service\ServiceVariantController;
use App\Http\Controllers\CustomerEnquiryController;
use App\Http\Controllers\Agent\Financial\AgentBankAccountController;
use App\Http\Controllers\CMS\ContactUsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * Agent
 */
Route::prefix('agent')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');

        Route::middleware('auth:api')->group(function () {
            Route::post('logout', 'logout');
            Route::controller(AgentController::class)->group(function () {
                Route::get('/info', 'show');
                Route::post('document/{agent}', 'updateDocument');
                Route::post('profile/{agent}', 'profilePictureUpload');
                Route::patch('{agent}', 'update');
            });
        });
    });
});

/*
*  Agent Financial
*/
Route::prefix('agent/bank')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::controller(AgentBankAccountController::class)->group(function () {
            Route::get('details', 'index');
            Route::post('account', 'store');
            Route::patch('{agentBankAccount}', 'update');
            Route::delete('{agentBankAccount}', 'destroy');
        });
    });
});

/*
* Country
*/
Route::controller(CountryController::class)->group(function () {
    Route::prefix('country')->group(function () {
        Route::get('all', 'index');
        Route::get('{country}', 'show');
        Route::post('add', 'create');
        Route::patch('{country}', 'update');
        Route::delete('{country}', 'destroy');
    });
});

/*
* State
*/
Route::controller(StateController::class)->group(function () {
    Route::prefix('state')->group(function () {
        Route::get('all', 'index');
        Route::get('{state}', 'show');
        Route::post('add', 'create');
        Route::patch('{state}', 'update');
        Route::delete('{state}', 'destroy');
    });
});

/*
* District
*/
Route::controller(DistrictController::class)->group(function () {
    Route::prefix('district')->group(function () {
        Route::get('all', 'index');
        Route::get('{district}', 'show');
        Route::post('add', 'create');
        Route::patch('{district}', 'update');
        Route::delete('{district}', 'destroy');
    });
});

/*
* City
*/
Route::controller(CityController::class)->group(function () {
    Route::prefix('city')->group(function () {
        Route::get('all', 'index');
        Route::get('{city}', 'show');
        Route::post('add', 'create');
        Route::patch('{city}', 'update');
        Route::delete('{city}', 'destroy');
    });
});

/*
* Manage Service
*/
Route::prefix('service')->group(function () {
    /*
    * Services
    */
    Route::controller(ServiceController::class)->group(function () {
        Route::get('all', 'index');
        Route::get('images', 'serviceFiles');
        Route::get('single/{service}', 'show');
        Route::get('{service}', 'getServiceVariants');
        Route::middleware('auth:api')->group(function () {
            Route::post('add', 'create');
            Route::post('bulk/import', 'import');
            Route::post('{service}', 'update');
            Route::delete('{service}', 'destroy');
        });
    });
    /*
    * Service categories
    */
    Route::controller(ServiceCategoryController::class)->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('all', 'index');
            Route::get('available', 'getAvailableServices');
            Route::get('upcoming', 'getUpcomingServices');
            Route::get('images/{serviceCategory}', 'categoryFiles');
            Route::get('single/{serviceCategory}', 'show');
            Route::get('slider/{slug}', 'getSlider');
            Route::get('{slug}', 'getServices');
            Route::middleware('auth:api')->group(function () {
                Route::post('add', 'create');
                Route::post('slider', 'slider');
                Route::post('{serviceCategory}', 'update');
                Route::delete('{serviceCategory}', 'destroy');
            });
        });
    });
    /*
     * Service Tags
     */
    Route::controller(ServiceTagController::class)->group(function () {
        Route::prefix('tag')->group(function () {
            Route::get('all', 'index');
            Route::get('{serviceTag}', 'show');
            Route::middleware('auth:api')->group(function () {
                Route::post('add', 'create');
                Route::patch('{serviceTag}', 'update');
                Route::delete('{serviceTag}', 'destroy');
            });
        });
    });
    /*
     * Service Variants
     */
    Route::controller(ServiceVariantController::class)->group(function () {
        Route::prefix('variant')->group(function () {
            Route::get('all', 'index');
            Route::get('{serviceVariant}', 'show');
            Route::middleware('auth:api')->group(function () {
                Route::post('add', 'create');
                Route::patch('{serviceVariant}', 'update');
                Route::delete('{serviceVariant}', 'destroy');
            });
        });
    });
});

Route::controller(CustomerEnquiryController::class)->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('enquiries', 'index');
        Route::post('enquiry', 'create');
    });
});

Route::controller(ContactUsController::class)->group(function () {
    Route::prefix('cms')->group(function () {
        Route::post('contact-us', 'store');
    });
});

