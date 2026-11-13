<?php

declare(strict_types=1);

namespace Modules\RealProjects\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\RealProjects\Entities\RealProject;
use Modules\RealProjects\Repositories\Eloquent\EloquentRealProjectRepository;
use Modules\RealProjects\Repositories\RealProjectRepository;

/**
 * Class RealProjectsServiceProvider
 * @package Modules\RealProjects\Providers
 */
class RealProjectsServiceProvider extends ServiceProvider
{
    /** @var string Module name. */
    protected string $moduleName = 'RealProjects';

    /** @var string Module name in lowercase (config and translation namespace). */
    protected string $moduleNameLower = 'real_projects';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(RealProjectRepository::class, function () {
            return new EloquentRealProjectRepository(new RealProject());
        });
    }

    /**
     * Register config.
     */
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

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
