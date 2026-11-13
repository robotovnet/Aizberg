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

use Modules\Settings\Http\Controllers\SettingsController;

Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['ajax', 'auth:staff']], function () {
    $settings = 'Modules\Settings\Entities\Settings';

    Route::get('all', [SettingsController::class, 'index'])
        //->middleware('can:view,' . $settings)
        ->name('all');

    Route::get('get-by-code/{setting_code}', [SettingsController::class, 'getByCode'])
        //->middleware('can:view,' . $settings)
        ->name('getByCode');

    Route::patch('{setting_id}', [SettingsController::class, 'update'])
        ->middleware('can:management,' . $settings)
        ->name('update');
});
