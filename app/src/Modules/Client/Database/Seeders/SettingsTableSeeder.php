<?php

declare(strict_types=1);

namespace Modules\Client\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

/**
 * Class SettingsTableSeeder
 * @package Modules\Client\Database\Seeders
 */
class SettingsTableSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'settings';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert(
                [
                    'code'              => Config::get('client.settings_codes.show_captcha_at_registration'),
                    'order'             => 1000,
                    'name'              => Str::getLangJson([
                                               'ru' => 'Выводить каптчу при регистрации клиента?',
                                               'en' => 'Display captcha when registering a client?',
                                           ]),
                    'value'             => 'no',
                    'section_code'      => 'REGISTRATION',
                    'type_form_element' => 'checkbox',
                    'created_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
