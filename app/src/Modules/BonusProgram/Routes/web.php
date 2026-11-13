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

use Modules\BonusProgram\Http\Controllers\BonusProgramController;

Route::group([
             'prefix' => 'bonus-program',
             'as' => 'bonusProgram.',
             'middleware' => ['ajax', 'auth:staff']
         ], function () {
            $program   = 'Modules\BonusProgram\Entities\BonusLevel';

            // Groups
            Route::post('store', [BonusProgramController::class, 'store'])
                ->middleware('can:management,' . $program)
                ->name('bonus_program.store');

            Route::patch('{bonus_level_id}', [BonusProgramController::class, 'update'])
                ->middleware('can:management,' . $program)
                ->name('bonus_program.update');

            Route::delete('{bonus_level_id}', [BonusProgramController::class, 'destroy'])
                ->middleware('can:management,' . $program)
                ->name('bonus_program.delete');

            Route::get('{bonus_level_id}', [BonusProgramController::class, 'show'])
                ->middleware('can:view,' . $program)
                ->name('bonus_program.show');

            Route::get('all', [BonusProgramController::class, 'index'])
                ->middleware('can:view,' . $program)
                ->name('bonus_program.all');

            Route::post('blocked-sections', [BonusProgramController::class, 'blockingSections'])
                ->middleware('can:management,' . $program)
                ->name('blocked_sections');

            Route::get('blocked-sections', [BonusProgramController::class, 'getBlockedSections'])
                ->middleware('can:view,' . $program)
                ->name('blocked_sections_all');
         });
