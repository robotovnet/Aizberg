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

use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Controllers\ReportsController;

Route::group([
     'prefix' => 'orders',
     'as' => 'orders.',
     'middleware' => ['ajax', 'auth:staff']
 ], function () {
    $order = 'Modules\Order\Entities\Order';

    Route::get('all', [OrderController::class, 'all'])
    ->middleware('can:view,' . $order)
    ->name('all');

    Route::get('reports/productsInOrders/{from_date}/{to_date}', [ReportsController::class, 'productsInOrdersReport'])
    ->middleware('can:view,' . $order)
    ->withoutMiddleware('ajax')
    ->name('productsInOrdersReport');

    Route::get('details/{order_id}', [OrderController::class, 'details'])
    ->middleware('can:view,' . $order)
    ->name('details');

    Route::get('thanks-feedback/{order_id}', [OrderController::class, 'thanksFeedback'])
    ->middleware('can:management,' . $order)
    ->name('thanks_feedback');

    Route::post('change-status/{order_id}', [OrderController::class, 'changeStatus'])
    ->middleware('can:management,' . $order)
    ->name('change_status');

    Route::post('change-method-pay/{order_id}', [OrderController::class, 'changeMethodPay'])
    ->middleware('can:management,' . $order)
    ->name('change_method_pay');

    Route::post('create', [OrderController::class, 'create'])
    ->middleware('can:management,' . $order)
    ->name('create');

    Route::post('change-tracknumber/{order_id}', [OrderController::class, 'changeTracknumber'])
    ->middleware('can:management,' . $order)
    ->name('change_tracknumber');
 });
