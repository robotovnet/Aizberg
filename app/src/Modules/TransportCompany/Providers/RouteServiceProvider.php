<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\TransportCompany\Entities\CourierCity;
use Modules\TransportCompany\Entities\DeliverySettings;
use Modules\TransportCompany\Entities\PaymentMethod;
use Modules\TransportCompany\Entities\PickupPoint;
use Modules\TransportCompany\Entities\Postamat;
use Modules\TransportCompany\Entities\ShippingCost;
use Modules\TransportCompany\Entities\Warehouse;
use Modules\TransportCompany\Entities\ProductMarkup;
use Modules\TransportCompany\Repositories\CourierCityRepository;
use Modules\TransportCompany\Repositories\DeliverySettingsRepository;
use Modules\TransportCompany\Repositories\PaymentMethodRepository;
use Modules\TransportCompany\Repositories\PickupPointRepository;
use Modules\TransportCompany\Repositories\PostamatRepository;
use Modules\TransportCompany\Repositories\ShippingCostRepository;
use Modules\TransportCompany\Repositories\TransportCompanyRepository;
use Modules\TransportCompany\Repositories\WarehouseRepository;
use Modules\TransportCompany\Repositories\ProductMarkupRepository;
use Modules\TransportCompany\Entities\TransportCompany;

/**
 * Class RouteServiceProvider
 * @package Modules\TransportCompany\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected string $moduleNamespace = 'Modules\TransportCompany\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('transport_company_id', '^[0-9]+$');
        Route::pattern('delivery_settings_id', '^[0-9]+$');
        Route::pattern('courier_city_id', '^[0-9]+$');
        Route::pattern('pickup_point_id', '^[0-9]+$');
        Route::pattern('postamat_id', '^[0-9]+$');
        Route::pattern('warehouse_id', '^[0-9]+$');
        Route::pattern('adding_method', '^(all|preset|manual)$');
        Route::pattern('page', '^[0-9]+$');
        Route::pattern('city', '^[0-9]+$');
        Route::pattern('shipping_cost_id', '^[0-9]+$');
        Route::pattern('payment_method_id', '^[0-9]+$');
        Route::pattern('product_markup_id', '^[0-9]+$');

        Route::bind('transport_company_id', function ($transportCompanyId) {
            return app()->make(
                TransportCompanyRepository::class,
                ['model' => new TransportCompany()]
            )->setModel((int)$transportCompanyId);
        });

        Route::bind('delivery_settings_id', function ($deliverySettingsId) {
            return app()->make(
                DeliverySettingsRepository::class,
                ['model' => new DeliverySettings()]
            )->setModel((int)$deliverySettingsId);
        });

        Route::bind('courier_city_id', function ($courierCityId) {
            return app()->make(
                CourierCityRepository::class,
                ['model' => new CourierCity()]
            )->setModel((int)$courierCityId);
        });

        Route::bind('pickup_point_id', function ($pickupPointId) {
            return app()->make(
                PickupPointRepository::class,
                ['model' => new PickupPoint()]
            )->setModel((int)$pickupPointId);
        });

        Route::bind('postamat_id', function ($postamatId) {
            return app()->make(
                PostamatRepository::class,
                ['model' => new Postamat()]
            )->setModel((int)$postamatId);
        });

        Route::bind('warehouse_id', function ($warehouseId) {
            return app()->make(
                WarehouseRepository::class,
                ['model' => new Warehouse()]
            )->setModel((int)$warehouseId);
        });

        Route::bind('payment_method_id', function ($paymentMethodId) {
            return app()->make(
                PaymentMethodRepository::class,
                ['model' => new PaymentMethod()]
            )->setModel((int)$paymentMethodId);
        });

        Route::bind('product_markup_id', function ($productMarkupId) {
            return app()->make(
                ProductMarkupRepository::class,
                ['model' => new ProductMarkup()]
            )->setModel((int)$productMarkupId);
        });

        Route::bind('shipping_cost_id', function ($shippingCostId) {
            return app()->make(
                ShippingCostRepository::class,
                ['model' => new ShippingCost()]
            )->setModel((int)$shippingCostId);
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
            ->group(module_path('TransportCompany', '/Routes/web.php'));
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
            ->group(module_path('TransportCompany', '/Routes/api.php'));
    }
}
