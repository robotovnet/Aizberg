<?php

declare(strict_types=1);

use Modules\Lottery\Http\Controllers\LotteryDrawsController;
use Modules\Lottery\Http\Controllers\LotteryAppsController;
use Modules\Lottery\Http\Controllers\LotteryPrizesController;
use Modules\Lottery\Http\Controllers\LotteryTicketsController;

Route::group([
    'prefix' => 'lottery',
    'as' => 'lottery.',
    'middleware' => ['ajax', 'auth:staff']
], function () {
    Route::group([
        'prefix' => 'prize',
        'as' => 'prizes.',
        'middleware' => ['can:view,Modules\Lottery\Entities\LotteryPrizes']
    ], function () {
        Route::get('all', [LotteryPrizesController::class, 'all'])
            ->name('all');

        Route::group([
            'middleware' => ['can:management,Modules\Lottery\Entities\LotteryPrizes']
        ], function () {
            Route::post('add', [LotteryPrizesController::class, 'add'])
                ->name('add');

            Route::delete('delete/{prize_id}', [LotteryPrizesController::class, 'delete'])
                ->name('delete');
        });
    });

    Route::group([
        'prefix' => 'apps',
        'as' => 'apps.',
        'middleware' => ['can:management,Modules\Lottery\Entities\LotteryApps']
    ], function () {
        Route::get('all', [LotteryAppsController::class, 'all'])
            ->name('all');

        Route::get('detail/{app_id}', [LotteryAppsController::class, 'detail'])
            ->name('detail');

        Route::post('accept/{app_id}', [LotteryAppsController::class, 'accept'])
            ->name('accept');

        Route::post('reject/{app_id}', [LotteryAppsController::class, 'reject'])
            ->name('reject');

        Route::get('total', [LotteryAppsController::class, 'total'])
            ->name('total');

        Route::post('register', [LotteryAppsController::class, 'register'])
            ->name('register');
    });

    Route::group([
        'middleware' => ['can:management,Modules\Lottery\Entities\LotteryApps']
    ], function () {
        Route::get('participants/count', [LotteryTicketsController::class, 'getActiveParticipantsCount'])
            ->name('participants.count');
    });

    Route::group([
        'prefix' => 'draws',
        'as' => 'draws.',
        'middleware' => ['can:view,Modules\Lottery\Entities\LotteryDraws']
    ], function () {
        Route::get('all', [LotteryDrawsController::class, 'all'])
            ->name('all');
    });

    Route::group([
        'prefix' => 'tickets',
        'as' => 'tickets.',
        'middleware' => ['can:management,Modules\Lottery\Entities\LotteryTickets']
    ], function () {
        Route::get('all/{generation_id}', [LotteryTicketsController::class, 'getTickets'])
            ->withoutMiddleware('ajax')
            ->name('all');

        Route::post('generator', [LotteryTicketsController::class, 'generator'])
            ->name('generator');

        Route::get('total-reg-tickets', [LotteryTicketsController::class, 'getTotalRegisteredTickets'])
            ->name('total-reg-tickets');

        Route::get('generations', [LotteryTicketsController::class, 'getGenerations'])
            ->name('generations');
    });
});
