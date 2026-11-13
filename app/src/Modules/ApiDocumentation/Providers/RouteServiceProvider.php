<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\ApiDocumentation\Http\Controllers';

    public function boot(): void
    {
        parent::boot();

        Route::pattern('id', '[0-9]+');
        Route::pattern('documentationId', '[0-9]+');
        Route::pattern('sectionId', '[0-9]+');
    }

    public function map(): void
    {
        Route::prefix('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('ApiDocumentation', '/Routes/api.php'));
    }
}
