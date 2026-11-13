<?php

declare(strict_types=1);

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class SettingsSectionsTableSeeder
 * @package Modules\Language\Database\Seeders
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
                    'code'       => 'PAGINATION',
                    'order'      => 5000,
                    'name'       => Str::getLangJson([
                                                         'ru' => 'Пагинация',
                                                         'en' => 'Pagination',
                                                     ]),
                    'type'       => 'system',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
