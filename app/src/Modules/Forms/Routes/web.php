<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Forms\Http\Controllers\FormController;

Route::group([
    'prefix' => 'forms',
    'as' => 'forms.',
    'middleware' => ['ajax'],
], static function (): void {
    // Обратная связь: POST /forms/feedback
    Route::post('feedback', [FormController::class, 'feedback'])->name('feedback');
});
