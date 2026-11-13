<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\BonusProgram\Database\Seeders
 */
class StaffPermissionsSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_permissions';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $permissions = [
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление бонусной программой',
                                                     'en' => 'Bonus program management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Привилегии',
                                                     'en' => 'Privileges',
                                                 ]),
                'policy'     => 'bonusprogram.level.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Бонусная программа',
                                                     'en' => 'Bonus Program',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Привилегии',
                                                     'en' => 'Privileges',
                                                 ]),
                'policy'     => 'bonusprogram.level.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
