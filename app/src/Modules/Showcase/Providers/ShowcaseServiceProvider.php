<?php

declare(strict_types=1);

namespace Modules\Showcase\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Showcase\Repositories\ShowcaseRepository;
use Modules\Showcase\Services\ShowcaseImageService;
use Modules\Showcase\Services\ShowcasePermissionService;

class ShowcaseServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Showcase';

    protected string $moduleNameLower = 'showcase';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(ShowcaseImageService::class);
        $this->app->singleton(ShowcasePermissionService::class);
        $this->app->singleton(ShowcaseRepository::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }
}
