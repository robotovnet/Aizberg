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

use Modules\Client\Http\Controllers\AuthController;
use Modules\Staff\Http\Controllers\ProfileController;
use Modules\Client\Http\Controllers\PasswordRestoreController;
use Modules\Staff\Http\Controllers\GroupController;
use Modules\Staff\Http\Controllers\PermissionsController;
use Modules\Staff\Http\Controllers\StaffController;

Route::group(['prefix' => 'staff', 'middleware' => ['ajax']], function () {
    Route::get('session/{session_id}', [AuthController::class, 'isSessionActive'])
        ->name('staff.is_session_active');

    Route::group(['middleware' => ['guest']], function () {
        Route::post('login-by-phone', [AuthController::class, 'login'])
            ->name('staff.login_by_phone');
        Route::post('login-by-email', [AuthController::class, 'login'])
            ->name('staff.login_by_email');

        Route::post('password-recovery/by-phone/first-step', [PasswordRestoreController ::class, 'firstStepByPhone'])
            ->name('staff.password_restore.by_phone.first_step');
        Route::post('password-recovery/by-email/first-step', [PasswordRestoreController ::class, 'firstStepByEmail'])
            ->name('staff.password_restore.by_email.first_step');

        Route::post('password-recovery/second-step', [PasswordRestoreController ::class, 'secondStep'])
            ->name('staff.password_restore.second_step');

        Route::post('password-recovery/third-step', [PasswordRestoreController ::class, 'thirdStep'])
            ->name('staff.password_restore.third_step');
    });

    Route::group(['middleware' => ['auth:staff']], function () {
        $staff = 'Modules\Staff\Entities\Staff';
        $group = 'Modules\Staff\Entities\StaffGroup';

        Route::get('alive', function () {
            return response()->json(['timestamp' => time()]);
        })->name('staff.alive');

        Route::post('logout', [AuthController::class, 'logout'])
            ->name('staff.logout');

        Route::get('permissions', [PermissionsController::class, 'index'])
            ->name('staff.permissions.all');

        Route::get('profile', [ProfileController::class, 'profile'])
            ->name('staff.profile');

        Route::patch('profile/name', [ProfileController::class, 'updateName'])
            ->name('staff.update_name');

        Route::patch('profile/email', [ProfileController::class, 'updateEmail'])
            ->name('staff.update_email');

        Route::patch('profile/phone', [ProfileController::class, 'updatePhone'])
            ->name('staff.update_phone');

        Route::get('profile/email', [ProfileController::class, 'updateEmailStart'])
            ->name('staff.update_email_start');

        Route::get('profile/phone', [ProfileController::class, 'updatePhoneStart'])
            ->name('staff.update_phone_start');

        Route::patch('profile/password', [ProfileController::class, 'updatePassword'])
            ->name('staff.update_password');

        Route::post('profile/userpic', [ProfileController::class, 'updateUserpic'])
            ->name('staff.update_userpic');

        Route::post('groups', [GroupController::class, 'store'])
            ->middleware('can:management,' . $group)
            ->name('staff.groups.store');

        Route::delete('groups/{group_id}', [GroupController::class, 'destroy'])
            ->middleware('can:management,' . $group)
            ->name('staff.groups.delete');

        Route::get('groups', [GroupController::class, 'index'])
            //->middleware('can:view,' . $group)
            ->name('staff.groups.all');

        Route::get('groups/{group_id}', [GroupController::class, 'show'])
            ->middleware('can:view,' . $group)
            ->name('staff.groups.show');

        Route::put('groups/{group_id}', [GroupController::class, 'update'])
            ->middleware('can:management,' . $group)
            ->name('staff.groups.update');

        Route::post('store', [StaffController::class, 'store'])
            ->middleware('can:management,' . $staff)
            ->name('staff.store');

        Route::patch('set-password/{staff_id}', [StaffController::class, 'setPassword'])
            ->middleware('can:management,' . $staff)
            ->name('staff.set_password');

        Route::post('upload-userpic/{staff_id}', [StaffController::class, 'uploadUserpic'])
            ->middleware('can:management,' . $staff)
            ->name('staff.upload_userpic');

        Route::patch('update/{staff_id}', [StaffController::class, 'update'])
            ->middleware('can:management,' . $staff)
            ->name('staff.update');

        Route::patch('block/{staff_id}', [StaffController::class, 'block'])
            ->middleware('can:management,' . $staff)
            ->name('staff.block');

        Route::patch('unblock/{staff_id}', [StaffController::class, 'unblock'])
            ->middleware('can:management,' . $staff)
            ->name('staff.unblock');

        Route::get('profile/{staff_id}', [StaffController::class, 'profile'])
            ->middleware('can:view,' . $staff)
            ->name('staff.profile_by_id');

        Route::get('all/{groupId?}', [StaffController::class, 'index'])
            //->middleware('can:view,' . $staff)
            ->name('staff.all');
    });
});
