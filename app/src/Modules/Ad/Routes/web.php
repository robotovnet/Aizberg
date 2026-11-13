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

use Modules\Ad\Http\Controllers\YmlController;

Route::group([
         'prefix' => 'ad',
         'as' => 'ad.',
         'middleware' => ['ajax', 'auth:staff']
     ], function () {
            $yml   = 'Modules\Ad\Entities\Yml';

            // Ymls
            Route::post('ymls/store', [YmlController::class, 'store'])
                ->middleware('can:management,' . $yml)
                ->name('yml.store');

            Route::patch('ymls/{yml_id}', [YmlController::class, 'update'])
                ->middleware('can:management,' . $yml)
                ->name('yml.update');

            Route::delete('ymls/{yml_id}', [YmlController::class, 'destroy'])
                ->middleware('can:management,' . $yml)
                ->name('yml.delete');

            Route::get('ymls/{yml_id}', [YmlController::class, 'show'])
                ->middleware('can:view,' . $yml)
                ->name('yml.show');

            Route::get('ymls/all', [YmlController::class, 'index'])
                ->middleware('can:view,' . $yml)
                ->name('yml.all');

            Route::post('ymls/sections', [YmlController::class, 'sections'])
                ->middleware('can:management,' . $yml)
                ->name('yml.sections');

            Route::get('ymls/sections', [YmlController::class, 'sectionsAll'])
                ->middleware('can:view,' . $yml)
                ->name('yml.sections_all');
     });
