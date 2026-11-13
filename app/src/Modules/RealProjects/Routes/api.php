<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\RealProjects\Http\Controllers\PublicRealProjectController;

// Публичный список для блока «В реальных проектах»: GET /api/real-projects
Route::get('real-projects', [PublicRealProjectController::class, 'index'])
    ->name('api.real-projects.index');
