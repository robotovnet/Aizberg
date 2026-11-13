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

use Modules\Material\Http\Controllers\MaterialController;

Route::group([
        'prefix' => 'materials',
        'as' => 'material.',
        'middleware' => ['ajax', 'auth:staff']
    ], function () {
        $material   = 'Modules\Material\Entities\Material';

        // Groups
        Route::post('store', [MaterialController::class, 'store'])
            ->middleware('can:management,' . $material)
            ->name('material.store');

        Route::get('all', [MaterialController::class, 'index'])
            ->middleware('can:view,' . $material)
            ->name('material.all');

        Route::get('sections', [MaterialController::class, 'getSections'])
            ->middleware('can:view,' . $material)
            ->name('material.sections');

        Route::patch('{material_id}', [MaterialController::class, 'update'])
            ->middleware('can:management,' . $material)
            ->name('material.update');

        Route::delete('{material_id}', [MaterialController::class, 'destroy'])
            ->middleware('can:management,' . $material)
            ->name('material.delete');

        Route::get('{material_id}', [MaterialController::class, 'show'])
            ->middleware('can:view,' . $material)
            ->name('material.show');
    });
