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

use Modules\Currency\Http\Controllers\CurrencyController;

Route::group(['prefix' => 'currencies', 'as' => 'currencies.', 'middleware' => ['ajax', 'auth:staff']], function () {
    $currency = 'Modules\Currency\Entities\Currency';

    Route::post('store', [CurrencyController::class, 'store'])
        ->middleware('can:management,' . $currency)
        ->name('store');

    Route::patch('{currency_id}', [CurrencyController::class, 'update'])
        ->middleware('can:management,' . $currency)
        ->name('update');

    Route::delete('{currency_id}', [CurrencyController::class, 'destroy'])
        ->middleware('can:management,' . $currency)
        ->name('delete');

    Route::get('{currency_id}', [CurrencyController::class, 'show'])
        ->middleware('can:view,' . $currency)
        ->name('show');

    Route::get('all', [CurrencyController::class, 'index'])
        ->middleware('can:view,' . $currency)
        ->name('all');
});
