<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\MarketplaceModules\Http\Controllers\CommunityMarketplaceController;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModuleActivityController;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModuleFavoriteController;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModuleModerationController;
use Modules\MarketplaceModules\Http\Controllers\MarketplaceModulePurchaseController;
use Modules\MarketplaceModules\Http\Controllers\PublicMarketplaceController;
use Modules\MarketplaceModules\Http\Controllers\SellerMarketplaceModuleController;

Route::prefix('marketplace-modules')->group(function () {
    Route::middleware('api')->group(function () {
        Route::get('search', [PublicMarketplaceController::class, 'search']);
        Route::get('popular-searches', [PublicMarketplaceController::class, 'popularSearches']);
        Route::get('featured', [PublicMarketplaceController::class, 'featured']);
        Route::get('categories', [PublicMarketplaceController::class, 'categories']);
        Route::get('categories/{category}', [PublicMarketplaceController::class, 'byCategory']);

        Route::get('modules/{slug}/questions', [PublicMarketplaceController::class, 'questions']);
        Route::get('modules/{slug}/reviews', [PublicMarketplaceController::class, 'reviews']);
        Route::get('modules/{slug}/versions', [PublicMarketplaceController::class, 'versions']);
    });

    // The module card remains public, but needs the client session when one
    // exists so it can return the current client's favorite state.
    Route::middleware(['web', 'api'])
        ->get('modules/{slug}', [PublicMarketplaceController::class, 'show']);

    Route::middleware(['web', 'api', 'auth:client'])->group(function () {
        Route::get('my/reviews', [MarketplaceModuleActivityController::class, 'reviews']);
        Route::get('my/questions', [MarketplaceModuleActivityController::class, 'questions']);
        Route::get('my/purchases', [MarketplaceModulePurchaseController::class, 'index']);

        Route::post('debug/modules/{slug}/purchase', [MarketplaceModulePurchaseController::class, 'debugStore']);

        Route::get('favorites', [MarketplaceModuleFavoriteController::class, 'index']);
        Route::post('modules/{slug}/favorite', [MarketplaceModuleFavoriteController::class, 'store']);
        Route::delete('modules/{slug}/favorite', [MarketplaceModuleFavoriteController::class, 'destroy']);

        Route::post('/', [SellerMarketplaceModuleController::class, 'store']);
        Route::patch('{id}', [SellerMarketplaceModuleController::class, 'update']);
        Route::delete('{id}', [SellerMarketplaceModuleController::class, 'destroy']);
        Route::delete('{id}/screenshots/{screenshotId}', [SellerMarketplaceModuleController::class, 'destroyScreenshot']);
        Route::post('{id}/changelog', [SellerMarketplaceModuleController::class, 'storeChangelog']);
        Route::post('{id}/archive', [SellerMarketplaceModuleController::class, 'archive']);

        Route::prefix('moderation')->group(function () {
            Route::post('modules/{id}/block', [MarketplaceModuleModerationController::class, 'block']);
            Route::post('modules/{id}/unblock', [MarketplaceModuleModerationController::class, 'unblock']);
        });

        Route::post('modules/{slug}/questions', [CommunityMarketplaceController::class, 'askQuestion']);
        Route::post('questions/{questionId}/answers', [CommunityMarketplaceController::class, 'answerQuestion']);
        Route::post('modules/{slug}/reviews', [CommunityMarketplaceController::class, 'review']);
        Route::post(
            'modules/{slug}/reviews/{reviewId}/helpfulness',
            [CommunityMarketplaceController::class, 'markReviewHelpfulness']
        );
        Route::post('modules/{slug}/ratings', [CommunityMarketplaceController::class, 'rate']);
    });
});
