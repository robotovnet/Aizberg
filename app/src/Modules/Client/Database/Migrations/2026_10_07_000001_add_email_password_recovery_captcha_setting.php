<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddEmailPasswordRecoveryCaptchaSetting extends Migration
{
    public function up(): void
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        if (!DB::table('settings_sections')->where('code', 'PASSWORD_RECOVERY')->exists()) {
            DB::table('settings_sections')->insert([
                'code' => 'PASSWORD_RECOVERY',
                'order' => 1100,
                'name' => Str::getLangJson([
                    'ru' => 'Восстановление пароля клиента',
                    'en' => 'Client password recovery',
                ]),
                'type' => 'system',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (!DB::table('settings')->where('code', 'CAPTCHA_AT_EMAIL_RECOVERY')->exists()) {
            DB::table('settings')->insert([
                'code' => 'CAPTCHA_AT_EMAIL_RECOVERY',
                'order' => 1000,
                'name' => Str::getLangJson([
                    'ru' => 'Требовать каптчу при восстановлении пароля по email?',
                    'en' => 'Require captcha when recovering a password by email?',
                ]),
                'value' => 'yes',
                'section_code' => 'PASSWORD_RECOVERY',
                'type_form_element' => 'checkbox',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('code', 'CAPTCHA_AT_EMAIL_RECOVERY')->delete();

        if (!DB::table('settings')->where('section_code', 'PASSWORD_RECOVERY')->exists()) {
            DB::table('settings_sections')->where('code', 'PASSWORD_RECOVERY')->delete();
        }
    }
}
