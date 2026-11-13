<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Showcase\Http\Controllers\ShowcaseCategoryController;
use Modules\Showcase\Http\Controllers\ShowcaseExampleController;
use Modules\Showcase\Http\Controllers\ShowcaseModerationController;

Route::prefix('showcase')->group(function () {
    Route::middleware('api')->group(function () {
        Route::get('categories', [ShowcaseCategoryController::class, 'index']);
        Route::get('categories/{id}', [ShowcaseCategoryController::class, 'show']);
        Route::get('categories/{id}/examples', [ShowcaseExampleController::class, 'indexByCategory']);

        Route::get('modules', [ShowcaseExampleController::class, 'modules']);
        Route::get('modules/{moduleName}/examples', [ShowcaseExampleController::class, 'indexByModule']);

        Route::get('examples', [ShowcaseExampleController::class, 'index']);
        Route::get('examples/{id}', [ShowcaseExampleController::class, 'show']);
    });

    Route::middleware(['web', 'api', 'auth:client'])->group(function () {
        Route::post('categories', [ShowcaseCategoryController::class, 'store']);
        Route::patch('categories/{id}', [ShowcaseCategoryController::class, 'update']);
        Route::delete('categories/{id}', [ShowcaseCategoryController::class, 'destroy']);

        Route::get('my/examples', [ShowcaseExampleController::class, 'myExamples']);
        Route::post('examples', [ShowcaseExampleController::class, 'store']);
        Route::patch('examples/{id}', [ShowcaseExampleController::class, 'update']);

        Route::prefix('moderation')->group(function () {
            Route::get('pending', [ShowcaseModerationController::class, 'pending']);
            Route::get('examples/{id}', [ShowcaseModerationController::class, 'show']);
            Route::patch('examples/{id}', [ShowcaseModerationController::class, 'update']);
            Route::post('examples/{id}/approve', [ShowcaseModerationController::class, 'approve']);
            Route::post('examples/{id}/reject', [ShowcaseModerationController::class, 'reject']);
        });
    });
});
