<?php

use Illuminate\Http\Request;
use Modules\Material\Http\Controllers\PublicPageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/material', function (Request $request) {
    return $request->user();
});

// Публичные данные страницы: GET /api/pages/{slug}?shop_id=...
Route::get('pages/{slug}', [PublicPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_\-]+')
    ->name('api.pages.show');