<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Database\Seeders;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class SettingsTableSeeder
 * @package Modules\BonusProgram\Database\Seeders
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
                    'code'              => 'BP_CALC_DAY',
                    'order'             => 1000,
                    'name'              => Str::getLangJson([
                                                                'ru' => 'Расчетный день',
                                                                'en' => 'Settlement day',
                                                            ]),
                    'value'             => '1',
                    'section_code'      => 'BONUS_PROGRAM',
                    'placeholder'       => Str::getLangJson([
                                                                'ru' => 'День месяца',
                                                                'en' => 'Day of the month',
                                                            ]),
                    'type_form_element' => 'input',
                    'created_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
