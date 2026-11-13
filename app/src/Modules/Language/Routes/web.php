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

use Modules\Language\Http\Controllers\LanguageController;

Route::prefix('languages')->group(function () {
    Route::get('/set-active', [LanguageController::class, 'setLanguage'])
        ->name('languages.set_active');
});
