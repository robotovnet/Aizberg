<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\TransportCompany\Http\Controllers\TransportCompanyController;
use Modules\TransportCompany\Http\Controllers\DeliverySettingsController;
use Modules\TransportCompany\Http\Controllers\CourierCityController;
use Modules\TransportCompany\Http\Controllers\PickupPointController;
use Modules\TransportCompany\Http\Controllers\PostamatController;
use Modules\TransportCompany\Http\Controllers\WarehouseController;
use Modules\TransportCompany\Http\Controllers\ShippingCostController;
use Modules\TransportCompany\Http\Controllers\PaymentMethodController;
use Modules\TransportCompany\Http\Controllers\ProductMarkupController;

Route::group([
    'prefix' => 'transport-companies',
     'as' => 'transport_companies.',
     'middleware' => ['ajax', 'auth:staff']
    ], function () {
        $transportCompany = 'Modules\TransportCompany\Entities\TransportCompany';
        $warehouse   = 'Modules\TransportCompany\Entities\Warehouse';
        $paymentMethod = 'Modules\TransportCompany\Entities\PaymentMethod';

        // Transport Companies
        Route::post('store', [TransportCompanyController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('store');

        Route::patch('{transport_company_id}', [TransportCompanyController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('update');

        Route::delete('{transport_company_id}', [TransportCompanyController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('delete');

        Route::get('{transport_company_id}', [TransportCompanyController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('show');

        Route::get('all', [TransportCompanyController::class, 'index'])
            //->middleware('can:view,' . $transportCompany)
            ->name('all');

        Route::post('{transport_company_id}/icon', [TransportCompanyController::class, 'iconUpload'])
            ->middleware('can:management,' . $transportCompany)
            ->name('icon.upload');

        Route::delete('{transport_company_id}/icon', [TransportCompanyController::class, 'iconDelete'])
            ->middleware('can:management,' . $transportCompany)
            ->name('icon.delete');

        Route::post('order', [TransportCompanyController::class, 'order'])
            ->middleware('can:management,' . $transportCompany)
            ->name('order');

        // Delivery Settings
        Route::post('delivery-settings/store', [DeliverySettingsController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('delivery-settings.store');

        Route::patch('delivery-settings/{delivery_settings_id}', [DeliverySettingsController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('delivery-settings.update');

        Route::delete('delivery-settings/{delivery_settings_id}', [DeliverySettingsController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('delivery-settings.delete');

        Route::get('delivery-settings/{delivery_settings_id}', [DeliverySettingsController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('delivery-settings.show');

        // Courier Cities
        Route::post('courier-cities/store', [CourierCityController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('courier-cities.store');

        Route::patch('courier-cities/{courier_city_id}', [CourierCityController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('courier-cities.update');

        Route::delete('courier-cities/{courier_city_id}', [CourierCityController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('courier-cities.delete');

        Route::get('courier-cities/{courier_city_id}', [CourierCityController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('courier-cities.show');

        Route::get('courier-cities', [CourierCityController::class, 'index'])
            ->middleware('can:view,' . $transportCompany)
            ->name('courier-cities.all');

        Route::get('courier-cities/total', [CourierCityController::class, 'total'])
            //->middleware('can:management,' . $transportCompany)
            ->name('courier-cities.total');

        // Pickup points
        Route::post('pickup-points/store', [PickupPointController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('pickup-points.store');

        Route::patch('pickup-points/{pickup_point_id}', [PickupPointController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('pickup-points.update');

        Route::delete('pickup-points/{pickup_point_id}', [PickupPointController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('pickup-points.delete');

        Route::get('pickup-points/{pickup_point_id}', [PickupPointController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('pickup-points.show');

        Route::get('pickup-points', [PickupPointController::class, 'index'])
            ->middleware('can:view,' . $transportCompany)
            ->name('pickup-points.all');

        Route::get('pickup-points/total', [PickupPointController::class, 'total'])
            //->middleware('can:management,' . $transportCompany)
            ->name('pickup-points.total');

        Route::get('pickup-points/cities', [PickupPointController::class, 'cities'])
            //->middleware('can:management,' . $transportCompany)
            ->name('pickup-points.cities');

        // Postamats
        Route::post('postamats/store', [PostamatController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('postamats.store');

        Route::patch('postamats/{postamat_id}', [PostamatController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('postamats.update');

        Route::delete('postamats/{postamat_id}', [PostamatController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('postamats.delete');

        Route::get('postamats/{postamat_id}', [PostamatController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('postamats.show');

        Route::get('postamats', [PostamatController::class, 'index'])
            ->middleware('can:view,' . $transportCompany)
            ->name('postamats.all');

        Route::get('postamats/total', [PostamatController::class, 'total'])
            //->middleware('can:management,' . $transportCompany)
            ->name('postamats.total');

        Route::get('postamats/cities', [PostamatController::class, 'cities'])
            //->middleware('can:management,' . $transportCompany)
            ->name('postamats.cities');

        // Warehouses
        Route::post('warehouses/store', [WarehouseController::class, 'store'])
            ->middleware('can:management,' . $warehouse)
            ->name('warehouses.store');

        Route::patch('warehouses/{warehouse_id}', [WarehouseController::class, 'update'])
            ->middleware('can:management,' . $warehouse)
            ->name('warehouses.update');

        Route::delete('warehouses/{warehouse_id}', [WarehouseController::class, 'destroy'])
            ->middleware('can:management,' . $warehouse)
            ->name('warehouses.delete');

        Route::get('warehouses/{warehouse_id}', [WarehouseController::class, 'show'])
            ->middleware('can:view,' . $warehouse)
            ->name('warehouses.show');

        Route::get('warehouses/all', [WarehouseController::class, 'index'])
            //->middleware('can:view,' . $warehouse)
            ->name('warehouses.all');

        // Shipping Cost
        Route::post('shipping-rates/store', [ShippingCostController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('shipping-rates.store');

        Route::patch('shipping-rates/{shipping_cost_id}', [ShippingCostController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('shipping-rates.update');

        Route::delete('shipping-rates/{shipping_cost_id}', [ShippingCostController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('shipping-rates.delete');

        Route::get('shipping-rates/{shipping_cost_id}', [ShippingCostController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('shipping-rates.show');

        Route::get('shipping-rates/all', [ShippingCostController::class, 'index'])
            ->middleware('can:view,' . $transportCompany)
            ->name('shipping-rates.all');

        // Payment Methods
        Route::post('payment-methods/store', [PaymentMethodController::class, 'store'])
            ->middleware('can:management,' . $paymentMethod)
            ->name('payment-methods.store');

        Route::patch('payment-methods/{payment_method_id}', [PaymentMethodController::class, 'update'])
            ->middleware('can:management,' . $paymentMethod)
            ->name('payment-methods.update');

        Route::delete('payment-methods/{payment_method_id}', [PaymentMethodController::class, 'destroy'])
            ->middleware('can:management,' . $paymentMethod)
            ->name('payment-methods.delete');

        Route::get('payment-methods/{payment_method_id}', [PaymentMethodController::class, 'show'])
            ->middleware('can:view,' . $paymentMethod)
            ->name('payment-methods.show');

        Route::get('payment-methods/all', [PaymentMethodController::class, 'index'])
            //->middleware('can:view,' . $paymentMethod)
            ->name('payment-methods.all');

        Route::post('payment-methods/order', [PaymentMethodController::class, 'order'])
            ->middleware('can:management,' . $paymentMethod)
            ->name('payment-methods.order');

        Route::post('payment-methods/{payment_method_id}/blocked-sections', [PaymentMethodController::class, 'blockingSections'])
            ->middleware('can:management,' . $paymentMethod)
            ->name('blocked_sections');

        Route::get('payment-methods/{payment_method_id}/blocked-sections', [PaymentMethodController::class, 'getBlockedSections'])
            ->middleware('can:view,' . $paymentMethod)
            ->name('blocked_sections_all');

        // Product Markups
        Route::post('product-markups/store', [ProductMarkupController::class, 'store'])
            ->middleware('can:management,' . $transportCompany)
            ->name('product-markups.store');

        Route::patch('product-markups/{product_markup_id}', [ProductMarkupController::class, 'update'])
            ->middleware('can:management,' . $transportCompany)
            ->name('product-markups.update');

        Route::delete('product-markups/{product_markup_id}', [ProductMarkupController::class, 'destroy'])
            ->middleware('can:management,' . $transportCompany)
            ->name('product-markups.delete');

        Route::get('product-markups/{product_markup_id}', [ProductMarkupController::class, 'show'])
            ->middleware('can:view,' . $transportCompany)
            ->name('product-markups.show');

        Route::get('product-markups/all', [ProductMarkupController::class, 'index'])
            ->middleware('can:view,' . $transportCompany)
            ->name('product-markups.all');
    });
