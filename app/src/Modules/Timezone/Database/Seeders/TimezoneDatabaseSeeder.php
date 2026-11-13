<?php

declare(strict_types=1);

namespace Modules\Timezone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class TimezoneDatabaseSeeder
 * @package Modules\Timezone\Database\Seeders
 */
class TimezoneDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'timezones';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $timezones = [
            [
                'header' => '{"ru":"Калининград, Россия",en:"Kaliningrad Time"}',
                'name' => 'Europe/Kaliningrad',
                'region' => 'Europe',
                'utc_offset' => '2'
            ],
            [
                'header' => '{"ru":"Москва, Россия",en:"Moscow Time"}',
                'name' => 'Europe/Moscow',
                'region' => 'Europe',
                'utc_offset' => '3'
            ],
            [
                'header' => '{"ru":"Самара, Россия",en:"Samara Time"}',
                'name' => 'Europe/Samara',
                'region' => 'Europe',
                'utc_offset' => '4'
            ],
            [
                'header' => '{"ru":"Екатеринбург, Россия",en:"Yekaterinburg Time"}',
                'name' => 'Asia/Yekaterinburg',
                'region' => 'Asia',
                'utc_offset' => '5'
            ],
            [
                'header' => '{"ru":"Омск, Россия",en:"Omsk Time"}',
                'name' => 'Asia/Omsk',
                'region' => 'Asia',
                'utc_offset' => '6'
            ],
            [
                'header' => '{"ru":"Красноярск, Россия",en:"Krasnoyarsk Time"}',
                'name' => 'Asia/Krasnoyarsk',
                'region' => 'Asia',
                'utc_offset' => '7'
            ],
            [
                'header' => '{"ru":"Иркутск, Россия",en:"Irkutsk Time"}',
                'name' => 'Asia/Irkutsk',
                'region' => 'Asia',
                'utc_offset' => '8'
            ],
            [
                'header' => '{"ru":"Якутск, Россия",en:"Yakutsk Time"}',
                'name' => 'Asia/Yakutsk',
                'region' => 'Asia',
                'utc_offset' => '9'
            ],
            [
                'header' => '{"ru":"Владивосток, Россия",en:"Vladivostok Time"}',
                'name' => 'Asia/Vladivostok',
                'region' => 'Asia',
                'utc_offset' => '10'
            ],
            [
                'header' => '{"ru":"Магадан, Россия",en:"Magadan Time"}',
                'name' => 'Asia/Magadan',
                'region' => 'Asia',
                'utc_offset' => '11'
            ],
            [
                'header' => '{"ru":"Петропавловск-Камчатский, Россия",en:"Kamchatka Time"}',
                'name' => 'Asia/Kamchatka',
                'region' => 'Asia',
                'utc_offset' => '12'
            ],
            [
                'header' => '{"ru":"Минск, Беларусь",en:"Minsk Time"}',
                'name' => 'Europe/Minsk',
                'region' => 'Europe',
                'utc_offset' => '3'
            ],
        ];

        DB::table($this->table)->insert($timezones);
    }
}
