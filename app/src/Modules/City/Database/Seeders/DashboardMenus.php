<?php

declare(strict_types=1);

namespace Modules\City\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class DashboardMenus
 * @package Modules\City\Database\Seeders
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
                                     'ru' => 'Города',
                                     'en' => 'Cities',
                                ]),
                'code'         => '3_cities',
                'href'         => '/dashboard/cities',
                'parent'       => '2_cities_warehouses',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'city.view',
                'order'        => '300',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
