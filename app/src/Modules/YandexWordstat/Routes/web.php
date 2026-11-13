<?php

use Modules\YandexWordstat\Http\Controllers\WordstatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'yandex-wordstat', 'as' => 'yandex-wordstat.', 'middleware' => ['ajax', 'auth:staff']], function () {
    $wordstat = 'Modules\YandexWordstat\Entities\YandexWordstat';

    Route::post('/uploadExcel', [WordstatController::class, 'uploadExcel'])
        ->middleware('can:management,' . $wordstat)
        ->name('uploadExcel');
        
    Route::get('/balance', [WordstatController::class, 'getBalance'])
        ->middleware('can:view,' . $wordstat)
        ->name('balance');

    Route::get('/clearStat', [WordstatController::class, 'clearStat'])
        ->middleware('can:management,' . $wordstat)
        ->name('clearStat');

    Route::get('/tasksInWork', [WordstatController::class, 'tasksInWork'])
        ->middleware('can:view,' . $wordstat)
        ->name('tasksInWork');

    Route::get('/downloadExcel', [WordstatController::class, 'downloadExcel'])
        ->middleware('can:view,' . $wordstat)
        ->withoutMiddleware('ajax')
        ->name('downloadExcel');

    Route::get('/clearJobs', [WordstatController::class, 'clearJobs'])
        ->middleware('can:management,' . $wordstat)
        ->name('clearJobs');
});
