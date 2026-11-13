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
 * Class StaffPermissionsSeeder
 * @package Modules\Catalog\Database\Seeders
 */
class StaffPermissionsSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_permissions';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $permissions = [
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр типов товаров',
                                                     'en' => 'View product types',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Типы товаров',
                                                     'en' => 'Product Types',
                                                 ]),
                'policy'     => 'catalog.groups.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление типами товаров',
                                                     'en' => 'Product types management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Типы товаров',
                                                     'en' => 'Product Types',
                                                 ]),
                'policy'     => 'catalog.groups.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление параметрами товаров',
                                                     'en' => 'Product features management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Параметры товаров',
                                                     'en' => 'Product features',
                                                 ]),
                'policy'     => 'catalog.features.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр параметров товаров',
                                                     'en' => 'View product features',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Параметры товаров',
                                                     'en' => 'Product features',
                                                 ]),
                'policy'     => 'catalog.features.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление разделами каталога',
                                                     'en' => 'Catalog sections management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Каталог',
                                                     'en' => 'Catalog',
                                                 ]),
                'policy'     => 'catalog.sections.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр каталога',
                                                     'en' => 'View catalog sections',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Каталог',
                                                     'en' => 'Catalog',
                                                 ]),
                'policy'     => 'catalog.sections.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление фильтрами каталога',
                                                     'en' => 'Catalog filters management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Фильтры',
                                                     'en' => 'Filters',
                                                 ]),
                'policy'     => 'catalog.filters.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр фильтров каталога',
                                                     'en' => 'View catalog filters',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Фильтры',
                                                     'en' => 'Filters',
                                                 ]),
                'policy'     => 'catalog.filters.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление товарами',
                                                     'en' => 'Catalog product management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Товары',
                                                     'en' => 'Products',
                                                 ]),
                'policy'     => 'catalog.products.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр товаров каталога',
                                                     'en' => 'View catalog products',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Товары',
                                                     'en' => 'Products',
                                                 ]),
                'policy'     => 'catalog.products.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр брендов',
                                                     'en' => 'View brands',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Бренды',
                                                     'en' => 'Brands',
                                                 ]),
                'policy'     => 'catalog.brands.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление брендами',
                                                     'en' => 'Brands management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Бренды',
                                                     'en' => 'Brands',
                                                 ]),
                'policy'     => 'catalog.brands.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр фидов данных',
                                                     'en' => 'Viewing data feeds',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Товары',
                                                     'en' => 'Products',
                                                 ]),
                'policy'     => 'feeds.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
