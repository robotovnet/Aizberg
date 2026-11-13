<?php

declare(strict_types=1);

namespace Modules\PriceType\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\PriceType\Database\Seeders
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
                                     'ru' => 'Типы цен',
                                     'en' => 'Price types',
                                ]),
                'code'         => '3_price_types',
                'href'         => '/dashboard/price-types',
                'parent'       => '2_prices',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'pricetype.view',
                'order'        => '500000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
