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

use Modules\ClientType\Http\Controllers\ClientTypeController;

Route::group([
        'prefix' => 'client-types',
        'as' => 'client_types.',
        'middleware' => ['ajax', 'auth:staff']
    ], function () {
        $clientType = 'Modules\ClientType\Entities\ClientType';

        Route::post('store', [ClientTypeController::class, 'store'])
            ->middleware('can:management,' . $clientType)
            ->name('store');

        Route::patch('{client_type_id}', [ClientTypeController::class, 'update'])
            ->middleware('can:management,' . $clientType)
            ->name('update');

        Route::delete('{client_type_id}', [ClientTypeController::class, 'destroy'])
            ->middleware('can:management,' . $clientType)
            ->name('delete');

        Route::get('{client_type_id}', [ClientTypeController::class, 'show'])
            ->middleware('can:view,' . $clientType)
            ->name('show');

        Route::get('all', [ClientTypeController::class, 'index'])
            //->middleware('can:view,' . $clientType)
            ->name('all');
    });
