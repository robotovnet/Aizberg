<?php

declare(strict_types=1);

namespace Modules\Forms\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package Modules\Forms\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /** @var string The module namespace to assume when generating URLs to actions. */
    protected $moduleNamespace = 'Modules\Forms\Http\Controllers';

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapWebRoutes();
    }

    /**
     * Public form routes. They need the "web" group: the captcha code
     * from /captcha/image is stored in the session.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Forms', '/Routes/web.php'));
    }
}
