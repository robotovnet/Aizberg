<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\TransportCompany\Database\Seeders
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
                                                     'ru' => 'Транспортные компании',
                                                     'en' => 'Transport companies',
                                                 ]),
                'code'         => '3_delivery',
                'href'         => '/dashboard/transport-companies',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'transportcompany.view',
                'order'        => '900000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Города доставки',
                                                     'en' => 'City of delivery',
                                                 ]),
                'code'         => '3_delivery_city',
                'href'         => '/dashboard/delivery-cities',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'transportcompany.view',
                'order'        => '900100',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Постаматы',
                                                     'en' => 'Postamats',
                                                 ]),
                'code'         => '3_postamats',
                'href'         => '/dashboard/postamats',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'transportcompany.view',
                'order'        => '900200',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Пункты выдачи заказов',
                                                     'en' => 'Pickup points',
                                                 ]),
                'code'         => '3_pickup_points',
                'href'         => '/dashboard/pickup-points',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'transportcompany.view',
                'order'        => '900300',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Склады',
                                                     'en' => 'Warehouses',
                                                 ]),
                'code'         => '3_warehouses',
                'href'         => '/dashboard/warehouses',
                'parent'       => '2_cities_warehouses',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'transportcompany.warehouses.view',
                'order'        => '200',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Методы оплаты',
                                                     'en' => 'Payment methods',
                                                 ]),
                'code'         => '3_payment_methods',
                'href'         => '/dashboard/payment-methods',
                'parent'       => '2_integrations',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'paymentmethods.view',
                'order'        => '20',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
