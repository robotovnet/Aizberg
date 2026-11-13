<?php

declare(strict_types=1);

namespace Modules\RealProjects\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package Modules\RealProjects\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /** @var string The module namespace to assume when generating URLs to actions. */
    protected $moduleNamespace = 'Modules\RealProjects\Http\Controllers';

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Staff (admin panel) routes: session, "ajax" and "auth:staff" middleware.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('RealProjects', '/Routes/web.php'));
    }

    /**
     * Public routes for the site, prefixed with /api.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('RealProjects', '/Routes/api.php'));
    }
}
