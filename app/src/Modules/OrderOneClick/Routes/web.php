<?php

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

use Modules\OrderOneClick\Http\Controllers\OrderOneClickController;

Route::group([
             'prefix' => 'order-one-click',
             'as' => 'orderOneClick.',
             'middleware' => ['ajax', 'auth:staff']
         ], function () {
            $oneClickOrder = 'Modules\OrderOneClick\Entities\OneClickOrder';

            Route::post('subscribers', [OrderOneClickController::class, 'setSubscribers'])
                ->middleware('can:management,' . $oneClickOrder)
                ->name('subscribers');

            Route::post('completed', [OrderOneClickController::class, 'completed'])
                ->middleware('can:view,' . $oneClickOrder)
                ->name('completed');

            Route::post('catalog-sections', [OrderOneClickController::class, 'setCatalogSections'])
                ->middleware('can:management,' . $oneClickOrder)
                ->name('catalog_sections');

            Route::get('subscribers', [OrderOneClickController::class, 'getSubscribers'])
                    ->middleware('can:management,' . $oneClickOrder)
                    ->name('get_subscribers');

            Route::get('catalog-sections', [OrderOneClickController::class, 'getCatalogSections'])
                    ->middleware('can:management,' . $oneClickOrder)
                    ->name('get_catalog_sections');

            Route::get('all', [OrderOneClickController::class, 'index'])
                ->middleware('can:view,' . $oneClickOrder)
                ->name('all');
         });
