<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\RealProjects\Entities\RealProject;
use Modules\RealProjects\Http\Controllers\RealProjectController;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
|
| PHP does not parse multipart/form-data in PUT requests, so a new banner
| is uploaded with POST /real-projects/{id}. PUT is kept for JSON updates.
|
*/

Route::group([
    'prefix' => 'real-projects',
    'as' => 'real-projects.',
    'middleware' => ['ajax', 'auth:staff'],
], static function (): void {
    $realProject = RealProject::class;

    Route::get('/', [RealProjectController::class, 'index'])
        ->middleware('can:view,' . $realProject)
        ->name('index');

    Route::get('{id}', [RealProjectController::class, 'show'])
        ->where('id', '[0-9]+')
        ->middleware('can:view,' . $realProject)
        ->name('show');

    Route::post('/', [RealProjectController::class, 'store'])
        ->middleware('can:management,' . $realProject)
        ->name('store');

    Route::post('{id}', [RealProjectController::class, 'update'])
        ->where('id', '[0-9]+')
        ->middleware('can:management,' . $realProject)
        ->name('update');

    Route::put('{id}', [RealProjectController::class, 'update'])
        ->where('id', '[0-9]+')
        ->middleware('can:management,' . $realProject);

    Route::delete('{id}', [RealProjectController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->middleware('can:management,' . $realProject)
        ->name('destroy');
});
