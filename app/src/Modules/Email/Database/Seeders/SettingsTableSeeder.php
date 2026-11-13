<?php

declare(strict_types=1);

namespace Modules\Email\Database\Seeders;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class SettingsTableSeeder
 * @package Modules\Email\Database\Seeders
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
                    'code'              => 'EMAIL_TRANSPORT',
                    'order'             => 6000,
                    'name'              => Str::getLangJson([
                                                                'ru' => 'Выберите транспорт',
                                                                'en' => 'Select transport',
                                                            ]),
                    'value'             => 'mailgun',
                    'section_code'      => 'EMAIL_TRANSPORTS',
                    'placeholder'       => Str::getLangJson([
                                                                'ru' => 'Выберите транспорт',
                                                                'en' => 'Select transport',
                                                            ]),
                    'items'             => '[{"header":"Mailgun","value":"mailgun"},{"header":"Yandex Cloud Postbox","value":"yandex"},{"header":"Rusender","value":"rusender"}]',
                    'type_form_element' => 'select',
                    'created_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
