<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ApiDocumentation\Repositories\ApiDocumentationRepository;
use Modules\ApiDocumentation\Services\DocumentationBuilderService;
use Modules\ApiDocumentation\Services\DocumentationSearchService;
use Modules\ApiDocumentation\Services\SectionHierarchyService;

class ApiDocumentationServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ApiDocumentation';

    protected string $moduleNameLower = 'apidocumentation';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(DocumentationBuilderService::class);
        $this->app->singleton(DocumentationSearchService::class);
        $this->app->singleton(SectionHierarchyService::class);
        $this->app->singleton(ApiDocumentationRepository::class);
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
