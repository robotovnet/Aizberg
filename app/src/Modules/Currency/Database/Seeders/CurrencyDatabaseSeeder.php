<?php

declare(strict_types=1);

namespace Modules\Currency\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CurrencyDatabaseSeeder
 * @package Modules\Currency\Database\Seeders
 */
class CurrencyDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'currencies';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $sections = [
            [
                'name'         => '{"ru":"Российский рубль","en":"Russian ruble"}',
                'code'         => 'RUB',
                'symbol'       => '₽',
                'is_default'   => 'Y',
                'extra_charge' => '0.00',
                'country_code' => 'ru',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Белорусский рубль","en":"Belarusian ruble"}',
                'code'         => 'BYN',
                'symbol'       => 'Br',
                'is_default'   => 'N',
                'extra_charge' => '0.00',
                'country_code' => 'by',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Доллар США","en":"United States dollar"}',
                'code'         => 'USD',
                'symbol'       => '$',
                'is_default'   => 'N',
                'extra_charge' => '0.00',
                'country_code' => 'us',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Евро","en":"Euro"}',
                'code'         => 'EUR',
                'symbol'       => '€',
                'is_default'   => 'N',
                'extra_charge' => '0.00',
                'country_code' => 'fr',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'         => '{"ru":"Фунт стерлингов","en":"British pound"}',
                'code'         => 'GBP',
                'symbol'       => '£',
                'is_default'   => 'N',
                'extra_charge' => '0.00',
                'country_code' => 'gb',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        DB::table($this->table)->insert($sections);

        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
