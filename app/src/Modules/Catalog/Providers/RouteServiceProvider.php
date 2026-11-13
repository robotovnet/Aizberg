<?php

declare(strict_types=1);

namespace Modules\Catalog\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Catalog\Entities\Brand;
use Modules\Catalog\Entities\Feature;
use Modules\Catalog\Entities\Filter;
use Modules\Catalog\Entities\Group;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\Section;
use Modules\Catalog\Repositories\BrandRepository;
use Modules\Catalog\Repositories\FeatureRepository;
use Modules\Catalog\Repositories\FilterRepository;
use Modules\Catalog\Repositories\GroupRepository;
use Modules\Catalog\Repositories\ProductRepository;
use Modules\Catalog\Repositories\SectionRepository;

/**
 * Class RouteServiceProvider
 * @package Modules\Catalog\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Catalog\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::pattern('catalog_group_id', '^[0-9]+$');
        Route::pattern('catalog_feature_id', '^[0-9]+$');
        Route::pattern('catalog_section_id', '^[0-9]+$');
        Route::pattern('catalog_filter_id', '^[0-9]+$');
        Route::pattern('product_id', '^[0-9]+$');
        //Route::pattern('uuid', '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89ABab][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$');
        Route::pattern('photo_id', '^[0-9]+$');
        Route::pattern('order', '^[0-9]+$');
        Route::pattern('main', '^main$');
        Route::pattern('brand_id', '^[0-9]+$');
        Route::pattern('delete_group_id', '^[0-9]+$');

        Route::bind('catalog_group_id', function ($groupId) {
            return app()->make(
                GroupRepository::class,
                ['model' => new Group()]
            )->setModel((int)$groupId);
        });

        Route::bind('catalog_feature_id', function ($featureId) {
            return app()->make(
                FeatureRepository::class,
                ['model' => new Feature()]
            )->setModel((int)$featureId);
        });

        Route::bind('catalog_section_id', function ($sectionId) {
            return app()->make(
                SectionRepository::class,
                ['model' => new Section()]
            )->setModel((int)$sectionId);
        });

        Route::bind('catalog_filter_id', function ($filterId) {
            return app()->make(
                FilterRepository::class,
                ['model' => new Filter()]
            )->setModel((int)$filterId);
        });

        Route::bind('product_id', function ($productId) {
            return app()->make(
                ProductRepository::class,
                ['model' => new Product()]
            )->setModel((int)$productId);
        });

        Route::bind('brand_id', function ($brandId) {
            return app()->make(
                BrandRepository::class,
                ['model' => new Brand()]
            )->setModel((int)$brandId);
        });
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
            ->group(module_path('Catalog', '/Routes/web.php'));
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
            ->group(module_path('Catalog', '/Routes/api.php'));
    }
}
