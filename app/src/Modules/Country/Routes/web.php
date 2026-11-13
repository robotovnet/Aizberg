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

use Modules\Country\Http\Controllers\CountryController;

Route::group(['prefix' => 'countries', 'as' => 'countries.', 'middleware' => ['ajax']], function () {

    Route::get('all', [CountryController::class, 'index'])
        ->name('all');

    Route::group(['middleware' => ['auth:staff']], function () {
        $country = 'Modules\Country\Entities\Country';

        Route::post('store', [CountryController::class, 'store'])
            ->middleware('can:management,' . $country)
            ->name('store');

        Route::patch('{country_id}', [CountryController::class, 'update'])
            ->middleware('can:management,' . $country)
            ->name('update');

        Route::delete('{country_id}', [CountryController::class, 'destroy'])
            ->middleware('can:management,' . $country)
            ->name('delete');

        Route::get('{country_id}', [CountryController::class, 'show'])
            ->middleware('can:view,' . $country)
            ->name('show');
    });
});
