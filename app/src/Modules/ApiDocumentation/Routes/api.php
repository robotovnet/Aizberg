<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\ApiDocumentation\Http\Controllers\DocumentationSectionController;
use Modules\ApiDocumentation\Http\Controllers\OwnerApiDocumentationController;
use Modules\ApiDocumentation\Http\Controllers\PublicApiDocumentationController;

Route::prefix('api-documentations')->group(function () {
    Route::middleware('api')->group(function () {
        Route::get('/', [PublicApiDocumentationController::class, 'index']);
        Route::get('search', [PublicApiDocumentationController::class, 'searchDocumentations']);
    });

    Route::middleware(['web', 'auth:client'])->group(function () {
        Route::get('manage', [OwnerApiDocumentationController::class, 'index']);
        Route::get('manage/{id}', [OwnerApiDocumentationController::class, 'show']);
        Route::post('/', [OwnerApiDocumentationController::class, 'store']);
        Route::patch('{id}', [OwnerApiDocumentationController::class, 'update']);
        Route::delete('{id}', [OwnerApiDocumentationController::class, 'destroy']);

        Route::post('{documentationId}/sections', [DocumentationSectionController::class, 'store']);
        Route::patch('{documentationId}/sections/{sectionId}', [DocumentationSectionController::class, 'update']);
        Route::delete('{documentationId}/sections/{sectionId}', [DocumentationSectionController::class, 'destroy']);
    });

    Route::middleware('api')->group(function () {
        Route::get('{slug}/search', [PublicApiDocumentationController::class, 'search']);
        Route::get('{slug}', [PublicApiDocumentationController::class, 'show']);
    });
});
