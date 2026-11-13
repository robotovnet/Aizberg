<?php

declare(strict_types=1);

namespace Modules\Blogs\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Blogs\Repositories\BlogRepository;
use Modules\Blogs\Services\BlogPermissionService;
use Modules\Blogs\Services\BlogPreviewImageService;

class BlogsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Blogs';

    protected string $moduleNameLower = 'blogs';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(BlogPreviewImageService::class);
        $this->app->singleton(BlogPermissionService::class);
        $this->app->singleton(BlogRepository::class);
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
