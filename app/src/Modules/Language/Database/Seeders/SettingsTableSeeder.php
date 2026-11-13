<?php

declare(strict_types=1);

namespace Modules\Language\Database\Seeders;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Facades\Config;

/**
 * Class SettingsTableSeeder
 * @package Modules\Language\Database\Seeders
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
                    'code'              => Config::get('language.settings_codes.default_language'),
                    'order'             => 2000,
                    'name'              => Str::getLangJson([
                                               'ru' => 'Язык по умолчанию',
                                               'en' => 'Default language',
                                           ]),
                    'value'             => 'ru',
                    'section_code'      => 'LANGUAGE_SETTINGS',
                    'placeholder'       => Str::getLangJson([
                                               'ru' => 'Код языка',
                                               'en' => 'Language code',
                                           ]),
                    'type_form_element' => 'input',
                    'created_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
