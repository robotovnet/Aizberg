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

use Modules\PriceType\Http\Controllers\PriceTypeController;

Route::group([
        'prefix' => 'price-types',
        'as' => 'price_types.',
        'middleware' => ['ajax', 'auth:staff']
     ], function () {
        $priceType = 'Modules\PriceType\Entities\PriceType';

        Route::get('all', [PriceTypeController::class, 'index'])
            //->middleware('can:view,' . $priceType)
            ->name('all');

        Route::get('{price_type_id}', [PriceTypeController::class, 'show'])
            ->middleware('can:view,' . $priceType)
            ->name('show');

        Route::post('store', [PriceTypeController::class, 'store'])
            ->middleware('can:management,' . $priceType)
            ->name('store');

        Route::patch('update/{price_type_id}', [PriceTypeController::class, 'update'])
            ->middleware('can:management,' . $priceType)
            ->name('update');

        Route::delete('{price_type_id}', [PriceTypeController::class, 'destroy'])
            ->middleware('can:management,' . $priceType)
            ->name('delete');
     });
