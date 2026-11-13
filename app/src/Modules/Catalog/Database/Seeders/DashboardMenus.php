<?php

declare(strict_types=1);

namespace Modules\Catalog\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\Catalog\Database\Seeders
 */
class DashboardMenus extends Seeder
{
    /** @var string Table name. */
    private string $table = 'dashboard_menus';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $permissions = [
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Типы товаров',
                                                       'en' => 'Product Types',
                                                   ]),
                'code'         => '3_product_types',
                'href'         => '/dashboard/product-types',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.groups.view',
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Подразделы каталога',
                                                       'en' => 'Сatalog Sections',
                                                   ]),
                'code'         => '3_catalog_sections',
                'href'         => '/dashboard/catalog-sections',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.sections.view',
                'order'        => '2000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Параметры товаров',
                                                       'en' => 'Features',
                                                   ]),
                'code'         => '3_features',
                'href'         => '/dashboard/features',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.features.view',
                'order'        => '3000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Фильтры',
                                                       'en' => 'Catalog Filters',
                                                   ]),
                'code'         => '3_catalog_filters',
                'href'         => '/dashboard/catalog-filters',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.filters.view',
                'order'        => '4000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Товары',
                                                       'en' => 'Products',
                                                   ]),
                'code'         => '3_products',
                'href'         => '/dashboard/products',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.products.view',
                'order'        => '100',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Плитки на гл. стр.',
                                                       'en' => 'Tiled catalog',
                                                   ]),
                'code'         => '3_tiled_catalog',
                'href'         => '/dashboard/tiled-catalog',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.sections.management',
                'order'        => '5000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'SEO для подразделов',
                                                       'en' => 'SEO for subsections',
                                                   ]),
                'code'         => '3_seo_catalog',
                'href'         => '/dashboard/seo-catalog',
                'parent'       => '2_directories',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.sections.management',
                'order'        => '30000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                                                       'ru' => 'Бренды',
                                                       'en' => 'Brands',
                                                   ]),
                'code'         => '3_brands',
                'href'         => '/dashboard/brands',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.brands.view',
                'order'        => '50',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                    'ru' => 'Миграция параметров',
                    'en' => 'Parameter migration',
                ]),
                'code'         => '3_product_parameter_migration',
                'href'         => '/dashboard/product-parameter-migration',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.features.view',
                'order'        => '7000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => Str::getLangJson([
                    'ru' => 'Товары по параметру',
                    'en' => 'Products by parameter',
                ]),
                'code'         => '3_products_by_parameter',
                'href'         => '/dashboard/products-by-parameter',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'catalog.features.view',
                'order'        => '8000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
