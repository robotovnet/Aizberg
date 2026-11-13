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

use Modules\FormValidation\Http\Controllers\FormValidationController;

Route::group(['prefix' => 'form-validation', 'middleware' => ['ajax']], function () {
    Route::get('staff/staffProfile', [FormValidationController::class, 'staffProfile']);
    Route::get('staff/profile', [FormValidationController::class, 'profile']);
    Route::get('transportCompany/createOrUpdate', [FormValidationController::class, 'transportCompany']);
    Route::get('{entity}/{request}', [FormValidationController::class, 'index']);
});
