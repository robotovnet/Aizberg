<?php

declare(strict_types=1);

namespace Modules\Showcase\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\Showcase\Http\Controllers';

    public function boot(): void
    {
        parent::boot();

        Route::pattern('id', '[0-9]+');
    }

    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Showcase', '/Routes/api.php'));
    }
}
