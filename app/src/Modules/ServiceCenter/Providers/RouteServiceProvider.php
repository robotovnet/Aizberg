<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\ServiceCenter\Repositories\ServiceCenterRepository;
use Modules\ServiceCenter\Entities\ServiceCenter;

/**
 * Class RouteServiceProvider
 * @package Modules\ServiceCenter\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected string $moduleNamespace = 'Modules\ServiceCenter\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('service_center_id', '^[0-9]+$');
        Route::pattern('page', '^[0-9]+$');
        Route::pattern('city', '^[0-9]+$');

        Route::bind('service_center_id', function ($serviceCenterId) {
            return app()->make(
                ServiceCenterRepository::class,
                ['model' => new ServiceCenter()]
            )->setModel((int)$serviceCenterId);
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('ServiceCenter', '/Routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('ServiceCenter', '/Routes/api.php'));
    }
}
