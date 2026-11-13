<?php

declare(strict_types=1);

namespace Modules\Shop\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\Shop\Database\Seeders
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
                        'ru' => 'Магазины',
                        'en' => 'Shops',
                ]),
                'code'         => '3_shops',
                'href'         => '/dashboard/shops',
                'parent'       => '2_cities_warehouses',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'shop.view',
                'order'        => '100',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                    'ru' => 'Фиды данных',
                    'en' => 'Feeds',
                ]),
                'code'         => '3_feeds',
                'href'         => '/dashboard/feeds',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'feeds.view',
                'order'        => '10000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],

        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
