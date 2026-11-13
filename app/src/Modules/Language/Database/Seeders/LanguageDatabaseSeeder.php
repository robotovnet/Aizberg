<?php

declare(strict_types=1);

namespace Modules\Language\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class LanguageDatabaseSeeder
 * @package Modules\Language\Database\Seeders
 */
class LanguageDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'languages';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $languages = [
            [
                'name'       => '{"ru":"Русский","en":"Russian"}',
                'code'       => 'ru'
            ],
            [
                'name'       => '{"ru":"Английский","en":"English"}',
                'code'       => 'en'
            ],
        ];

        DB::table($this->table)->insert($languages);

        $this->call(SettingsSectionsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }
}
