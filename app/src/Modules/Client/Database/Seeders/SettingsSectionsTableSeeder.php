<?php

declare(strict_types=1);

namespace Modules\Client\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class SettingsSectionsTableSeeder
 * @package Modules\Client\Database\Seeders
 */
class SettingsSectionsTableSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'settings_sections';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert(
                [
                    'code'       => 'REGISTRATION',
                    'order'      => 1000,
                    'name'       => Str::getLangJson([
                                        'ru' => 'Регистрация клиента',
                                        'en' => 'Client registration',
                                    ]),
                    'type' => 'system',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
