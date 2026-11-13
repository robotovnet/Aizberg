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

use Modules\City\Http\Controllers\CityController;

Route::group(['prefix' => 'cities', 'as' => 'cities.', 'middleware' => ['ajax']], function () {
    Route::post('/{country_code}/all', [CityController::class, 'getAllByCountryCode'])
        ->name('all_by_country_code');

    Route::get('total', [CityController::class, 'total'])
        ->name('total');

    Route::group(['middleware' => ['auth:staff']], function () {
        $city = 'Modules\City\Entities\City';

        Route::post('store', [CityController::class, 'store'])
            ->middleware('can:management,' . $city)
            ->name('store');

        Route::patch('{city_id}', [CityController::class, 'update'])
            ->middleware('can:management,' . $city)
            ->name('update');

        Route::delete('{city_id}', [CityController::class, 'destroy'])
            ->middleware('can:management,' . $city)
            ->name('delete');

        Route::get('{city_id}', [CityController::class, 'show'])
            //->middleware('can:view,' . $city)
            ->name('show');

        Route::get('all/{mode?}', [CityController::class, 'index'])
            //->middleware('can:view,' . $city)
            ->name('all');
    });
});
