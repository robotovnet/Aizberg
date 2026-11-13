<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\OrderOneClick\Database\Seeders
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
                'name'       => Str::getLangJson([
                                                     'ru' => 'Заказы в 1 клик',
                                                     'en' => 'One click orders',
                                                 ]),
                'code'         => '3_oneclick_orders',
                'href'         => '/dashboard/sales/one-click-order',
                'parent'       => '2_orders',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'orderoneclick.view',
                'order'        => '20000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Заказ в 1 клик',
                                                     'en' => 'One click order',
                                                 ]),
                'code'         => '3_oneclick_order',
                'href'         => '/dashboard/sections-one-click-order',
                'parent'       => '2_catalogue',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'orderoneclick.management',
                'order'        => '6000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
