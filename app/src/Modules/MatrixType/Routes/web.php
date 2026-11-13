<?php

use Illuminate\Support\Facades\Route;
use Modules\MatrixType\Http\Controllers\MatrixTypeController;
use Modules\MatrixType\Http\Controllers\MatrixTypeProductController;

Route::group(['prefix' => 'matrix-type', 'as' => 'matrix-type.', 'middleware' => ['ajax', 'auth:staff']], function () {
    $matrixType = 'Modules\MatrixType\Entities\MatrixType';

    Route::get('/', [MatrixTypeController::class, 'index'])
        ->middleware('can:view,' . $matrixType)
        ->name('index');

    Route::get('/{matrix_id}', [MatrixTypeController::class, 'show'])
        ->middleware('can:view,' . $matrixType)
        ->name('show');

    Route::post('/', [MatrixTypeController::class, 'store'])
        ->middleware('can:management,' . $matrixType)
        ->name('store');

    Route::put('/{matrix_id}', [MatrixTypeController::class, 'update'])
        ->middleware('can:management,' . $matrixType)
        ->name('update');

    Route::delete('/{matrix_id}', [MatrixTypeController::class, 'destroy'])
        ->middleware('can:management,' . $matrixType)
        ->name('destroy');

    Route::get('product/{productId}', [MatrixTypeProductController::class, 'getMatrixTypeId'])
        ->name('product.getMatrixTypeId');
});
