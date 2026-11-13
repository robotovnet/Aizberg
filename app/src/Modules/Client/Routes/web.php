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

use Modules\Client\Http\Controllers\RegisterController;
use Modules\Client\Http\Controllers\AuthController;
use Modules\Client\Http\Controllers\ClientController;
use Modules\Client\Http\Controllers\ClientUserpicController;
use Modules\Client\Http\Controllers\PasswordRestoreController;

Route::middleware(['api', 'auth:client'])
    ->post('api/client/userpic', [ClientUserpicController::class, 'store']);

Route::group(['prefix' => 'clients', 'middleware' => ['ajax']], function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::post('registration', [RegisterController::class, 'createNewClient'])
            ->name('clients.registration');

        Route::post('login-by-phone', [AuthController::class, 'login'])
            ->name('clients.login_by_phone');
        Route::post('login-by-email', [AuthController::class, 'login'])
            ->name('clients.login_by_email');

        Route::post('password-recovery/by-phone/first-step', [PasswordRestoreController ::class, 'firstStepByPhone'])
            ->name('clients.password_restore.by_phone.first_step');
        Route::post('password-recovery/by-email/first-step', [PasswordRestoreController ::class, 'firstStepByEmail'])
            ->name('clients.password_restore.by_email.first_step');

        Route::post('password-recovery/second-step', [PasswordRestoreController ::class, 'secondStep'])
            ->name('clients.password_restore.second_step');

        Route::post('password-recovery/third-step', [PasswordRestoreController ::class, 'thirdStep'])
            ->name('clients.password_restore.third_step');
    });

    Route::group(['middleware' => ['auth:client']], function () {
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('clients.logout');

        Route::post('change_password', [ClientController::class, 'updatePassword'])
            ->name('clients.change_password');

        Route::get('profile', [ClientController::class, 'profile'])
            ->name('clients.profile');

        Route::patch('profile', [ClientController::class, 'updateOwnProfile'])
            ->name('clients.update_own_profile');
    });

    Route::group(['middleware' => ['ajax', 'auth:staff']], function () {
        $client = 'Modules\Client\Entities\Client';

        Route::patch('profile/{client_id}', [ClientController::class, 'update'])
            ->middleware('can:management,' . $client)
            ->name('clients.update_profile');

        Route::get('profile/{client_id}', [ClientController::class, 'show'])
            ->middleware('can:view,' . $client)
            ->name('clients.show_profile');

        Route::get('all', [ClientController::class, 'all'])
            ->middleware('can:view,' . $client)
            ->name('clients.all');

        Route::delete('{client_id}', [ClientController::class, 'destroy'])
            ->middleware('can:management,' . $client)
            ->name('clients.delete');
    });
});
