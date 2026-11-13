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

use Modules\Shop\Http\Controllers\ShopController;

Route::group(
    [
        'prefix' => 'shops',
        'as' => 'shops.',
        'middleware' => ['ajax', 'auth:staff']
    ],
    function () {
        $shop = 'Modules\Shop\Entities\Shop';

        Route::get('all', [ShopController::class, 'index'])
            //->middleware('can:view,' . $shop)
            ->name('all');

        Route::get('{shop_id}', [ShopController::class, 'show'])
            ->middleware('can:view,' . $shop)
            ->name('show');

        Route::post('store', [ShopController::class, 'store'])
            ->middleware('can:management,' . $shop)
            ->name('store');

        Route::patch('{shop_id}', [ShopController::class, 'update'])
            ->middleware('can:management,' . $shop)
            ->name('update');

        Route::delete('{shop_id}', [ShopController::class, 'destroy'])
            ->middleware('can:management,' . $shop)
            ->name('delete');
    }
);
