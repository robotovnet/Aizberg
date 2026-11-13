<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Services\PhotoService;
use Modules\TransportCompany\Entities\CourierCity;
use Modules\TransportCompany\Entities\PaymentMethod;
use Modules\TransportCompany\Entities\PickupPoint;
use Modules\TransportCompany\Entities\Postamat;
use Modules\TransportCompany\Entities\DeliverySettings;
use Modules\TransportCompany\Entities\ProductMarkup;
use Modules\TransportCompany\Entities\ShippingCost;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Repositories\BlockedSectionsPMRepository;
use Modules\TransportCompany\Repositories\CourierCityRepository;
use Modules\TransportCompany\Repositories\DeliverySettingsRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentBlockedSectionsPMRepository;
use Modules\TransportCompany\Repositories\PaymentMethodRepository;
use Modules\TransportCompany\Repositories\PickupPointRepository;
use Modules\TransportCompany\Repositories\PostamatRepository;
use Modules\TransportCompany\Repositories\ProductMarkupRepository;
use Modules\TransportCompany\Repositories\TransportCompanyRepository;
use Modules\TransportCompany\Repositories\WarehouseRepository;
use Modules\TransportCompany\Repositories\ShippingCostRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentCourierCityRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentDeliverySettingsRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentPickupPointRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentTransportCompanyRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentPostamatRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentWarehouseRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentPaymentMethodRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentProductMarkupRepository;
use Modules\TransportCompany\Repositories\Eloquent\EloquentShippingCostRepository;
use Modules\TransportCompany\Entities\TransportCompany;
use Modules\TransportCompany\Services\IconService;
use Illuminate\Support\Facades\Config;

/**
 * Class TransportCompanyServiceProvider
 * @package Modules\TransportCompany\Providers
 */
class TransportCompanyServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'TransportCompany';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'transportCompany';

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(TransportCompanyRepository::class, function () {
            return new EloquentTransportCompanyRepository(new TransportCompany());
        });

        $this->app->bind(DeliverySettingsRepository::class, function () {
            return new EloquentDeliverySettingsRepository(new DeliverySettings());
        });

        $this->app->bind(CourierCityRepository::class, function () {
            return new EloquentCourierCityRepository(new CourierCity());
        });

        $this->app->bind(PickupPointRepository::class, function () {
            return new EloquentPickupPointRepository(new PickupPoint());
        });

        $this->app->bind(PostamatRepository::class, function () {
            return new EloquentPostamatRepository(new Postamat());
        });

        $this->app->bind(WarehouseRepository::class, function () {
            return new EloquentWarehouseRepository(new Warehouse());
        });

        $this->app->bind(ShippingCostRepository::class, function () {
            return new EloquentShippingCostRepository(new ShippingCost());
        });

        $this->app->bind(PaymentMethodRepository::class, function () {
            return new EloquentPaymentMethodRepository(new PaymentMethod());
        });

        $this->app->bind(ProductMarkupRepository::class, function () {
            return new EloquentProductMarkupRepository(new ProductMarkup());
        });

        $this->app->bind(IconService::class, function () {
            return new IconService(request()->file('icon'));
        });

        $this->app->bind(PhotoService::class, function () {
            return new PhotoService(request()->file('photo'));
        });

        $this->app->bind(BlockedSectionsPMRepository::class, function () {
            return new EloquentBlockedSectionsPMRepository();
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
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
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
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
