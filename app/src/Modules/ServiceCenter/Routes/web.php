<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\ServiceCenter\Http\Controllers\ServiceCenterController;

Route::group([
    'prefix' => 'service-centers',
     'as' => 'service_centers.',
     'middleware' => ['ajax', 'auth:staff']
    ], function () {
        $serviceCenter = 'Modules\ServiceCenter\Entities\ServiceCenter';

        // Pickup points
        Route::post('store', [ServiceCenterController::class, 'store'])
            ->middleware('can:management,' . $serviceCenter)
            ->name('service-centers.store');

        Route::patch('{service_center_id}', [ServiceCenterController::class, 'update'])
            ->middleware('can:management,' . $serviceCenter)
            ->name('service-centers.update');

        Route::delete('{service_center_id}', [ServiceCenterController::class, 'destroy'])
            ->middleware('can:management,' . $serviceCenter)
            ->name('service-centers.delete');

        Route::get('{service_center_id}', [ServiceCenterController::class, 'show'])
            ->middleware('can:view,' . $serviceCenter)
            ->name('service-centers.show');

        Route::get('all', [ServiceCenterController::class, 'index'])
            ->middleware('can:view,' . $serviceCenter)
            ->name('service-centers.all');

        Route::get('total', [ServiceCenterController::class, 'total'])
            ->name('service-centers.total');

        Route::get('cities', [ServiceCenterController::class, 'cities'])
            ->name('service-centers.cities');
    });
