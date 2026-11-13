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
                    'code'              => 'ENTITY_PER_PAGE',
                    'order'             => 5000,
                    'name'              => Str::getLangJson([
                                                                'ru' => 'Сколько объектов выводить на страницу?',
                                                                'en' => 'How many objects should I display on the page?',
                                                            ]),
                    'value'             => '10',
                    'section_code'      => 'PAGINATION',
                    'placeholder'       => null,
                    'type_form_element' => 'input',
                    'created_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
