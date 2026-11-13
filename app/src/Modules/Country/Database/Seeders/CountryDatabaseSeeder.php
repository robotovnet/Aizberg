<?php

declare(strict_types=1);

namespace Modules\Country\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CountryDatabaseSeeder
 * @package Modules\Country\Database\Seeders
 */
class CountryDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'countries';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $countries = [
            [
                'name'       => '{"ru":"Россия","en":"Russia"}',
                'code'       => 'ru',
                'language'   => 'ru',
                'is_default' => 'Y',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => '{"ru":"Белоруссия","en":"Belarus"}',
                'code'       => 'by',
                'language'   => 'ru',
                'is_default' => 'N',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        ];

        DB::table($this->table)->insert($countries);

        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
