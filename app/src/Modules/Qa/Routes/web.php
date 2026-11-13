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

use Modules\Qa\Http\Controllers\QaController;

Route::group([
        'prefix' => 'qa',
        'as' => 'qa.',
        'middleware' => ['ajax', 'auth:staff']
    ], function () {
        $qa = 'Modules\Qa\Entities\Qa';

        Route::post('store', [QaController::class, 'store'])
            ->middleware('can:management,' . $qa)
            ->name('qa.store');

        Route::post('answer/{qa_id}', [QaController::class, 'answer'])
            ->middleware('can:management,' . $qa)
            ->name('qa.answer');

        Route::get('all', [QaController::class, 'index'])
            ->middleware('can:view,' . $qa)
            ->name('qa.all');

        Route::patch('{qa_id}', [QaController::class, 'update'])
            ->middleware('can:management,' . $qa)
            ->name('qa.update');

        Route::delete('{qa_id}', [QaController::class, 'destroy'])
            ->middleware('can:management,' . $qa)
            ->name('qa.delete');

        Route::get('{qa_id}', [QaController::class, 'show'])
            ->middleware('can:view,' . $qa)
            ->name('qa.show');
    });
