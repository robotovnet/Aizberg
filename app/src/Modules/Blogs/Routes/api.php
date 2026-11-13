<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Blogs\Http\Controllers\BlogArticleController;
use Modules\Blogs\Http\Controllers\BlogCommentController;
use Modules\Blogs\Http\Controllers\BlogSectionController;

Route::prefix('blogs')->group(function () {
    Route::middleware('api')->group(function () {
        Route::get('sections', [BlogSectionController::class, 'index']);
        Route::get('sections/{id}', [BlogSectionController::class, 'show']);
        Route::get('sections/{id}/articles', [BlogArticleController::class, 'indexBySection']);

        Route::get('articles', [BlogArticleController::class, 'index']);
        Route::get('articles/{id}', [BlogArticleController::class, 'show']);
        Route::get('articles/{id}/comments', [BlogCommentController::class, 'index']);
    });

    Route::middleware(['web', 'api', 'auth:client'])->group(function () {
        Route::post('articles', [BlogArticleController::class, 'store']);
        Route::patch('articles/{id}', [BlogArticleController::class, 'update']);
        Route::delete('articles/{id}', [BlogArticleController::class, 'destroy']);
        Route::post('articles/{id}/archive', [BlogArticleController::class, 'archive']);

        Route::post('sections', [BlogSectionController::class, 'store']);
        Route::patch('sections/{id}', [BlogSectionController::class, 'update']);
        Route::delete('sections/{id}', [BlogSectionController::class, 'destroy']);

        Route::post('articles/{id}/comments', [BlogCommentController::class, 'store']);
        Route::post('comments/{commentId}/replies', [BlogCommentController::class, 'reply']);
        Route::patch('comments/{commentId}', [BlogCommentController::class, 'update']);
        Route::delete('comments/{commentId}', [BlogCommentController::class, 'destroy']);
    });
});
