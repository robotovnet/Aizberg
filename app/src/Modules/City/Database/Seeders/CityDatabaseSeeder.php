<?php

declare(strict_types=1);

namespace Modules\City\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CityDatabaseSeeder
 * @package Modules\City\Database\Seeders
 */
class CityDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'cities';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $cities = [
            [
                'name'         => '{"ru":"Москва","en":"Moscow"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'Y',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Санкт-Петербург","en":"St. Petersburg"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Екатеринбург","en":"Yekaterinburg"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Казань","en":"Kazan"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Ростов-на-Дону","en":"Rostov-on-Don"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Нижний Новгород","en":"Nizhny Novgorod"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Краснодар","en":"Krasnodar"}',
                'country_code' => 'ru',
                'timezone'     => 'Europe/Moscow',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Минск","en":"Minsk"}',
                'country_code' => 'by',
                'timezone'     => 'Europe/Minsk',
                'is_default'   => 'N',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        ];

        DB::table($this->table)->insert($cities);

        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
