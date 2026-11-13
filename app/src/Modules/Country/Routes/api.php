<?php

declare(strict_types=1);

use Illuminate\Http\Request;

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

use Modules\Country\Http\Controllers\CountryController;

//Route::group(['prefix' => 'countries', 'middleware' => ['guest', 'ajax']], function () {
//    Route::post('all', [CountryController::class, 'getAll'])
//        ->name('countries.all');
//});

//Route::middleware('auth:api')->get('/country', function (Request $request) {
//    return $request->user();
//});
