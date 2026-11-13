<?php

declare(strict_types=1);

namespace Modules\Catalog\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Entities\Brand;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\Filter;
use Modules\Catalog\Entities\Group;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\Catalog\Entities\Section;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Repositories\BrandRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentBrandRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentFeatureRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentFilterRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentGroupRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentProductPhotoRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentProductRepository;
use Modules\Catalog\Repositories\Eloquent\EloquentSectionRepository;
use Modules\Catalog\Repositories\FeatureRepository;
use Modules\Catalog\Repositories\FilterRepository;
use Modules\Catalog\Repositories\GroupRepository;
use Modules\Catalog\Repositories\ProductPhotoRepository;
use Modules\Catalog\Repositories\ProductRepository;
use Modules\Catalog\Repositories\SectionRepository;
use Modules\Catalog\Console\BackfillProductPhotoContentHashCommand;
use Modules\Catalog\Services\AdPhotoService;
use Modules\Catalog\Services\SectionIconService;

/**
 * Class CatalogServiceProvider
 * @package Modules\Catalog\Providers
 */
class CatalogServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Catalog';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'catalog';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                BackfillProductPhotoContentHashCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(GroupRepository::class, function () {
            return new EloquentGroupRepository(new Group());
        });

        $this->app->bind(FeatureRepository::class, function () {
            return new EloquentFeatureRepository(new Feature());
        });

        $this->app->bind(SectionRepository::class, function () {
            return new EloquentSectionRepository(new Section());
        });

        $this->app->bind(FilterRepository::class, function () {
            return new EloquentFilterRepository(new Filter());
        });

        $this->app->bind(ProductPhotoRepository::class, function () {
            return new EloquentProductPhotoRepository(new ProductPhoto());
        });

        $this->app->bind(ProductRepository::class, function () {
            return new EloquentProductRepository(new Product());
        });

        $this->app->bind(BrandRepository::class, function () {
            return new EloquentBrandRepository(new Brand());
        });

        $this->app->bind(SectionIconService::class, function () {
            return new SectionIconService(request()->file('icon'));
        });

        $this->app->bind(AdPhotoService::class, function () {
            return new AdPhotoService(request()->file('adphoto'));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
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
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
