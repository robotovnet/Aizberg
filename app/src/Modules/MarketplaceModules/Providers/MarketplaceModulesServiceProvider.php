<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MarketplaceModules\Console\RecompressModuleIconsCommand;
use Modules\MarketplaceModules\Repositories\MarketplaceModuleRepository;
use Modules\MarketplaceModules\Services\MarketplaceModuleIconService;
use Modules\MarketplaceModules\Services\MarketplaceModulePermissionService;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;
use Modules\MarketplaceModules\Services\MarketplaceRatingAggregatorService;

class MarketplaceModulesServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'MarketplaceModules';

    protected string $moduleNameLower = 'marketplacemodules';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(MarketplaceModuleIconService::class);
        $this->app->singleton(MarketplaceModulePermissionService::class);
        $this->app->singleton(MarketplaceModuleScreenshotService::class);
        $this->app->singleton(MarketplaceRatingAggregatorService::class);
        $this->app->singleton(MarketplaceModuleRepository::class);

        $this->commands([
            RecompressModuleIconsCommand::class,
        ]);
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
