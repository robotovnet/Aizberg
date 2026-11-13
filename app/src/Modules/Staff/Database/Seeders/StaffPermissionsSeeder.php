<?php

declare(strict_types=1);

namespace Modules\Staff\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\Staff\Database\Seeders
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
                                                     'ru' => 'Просмотр групп сотрудников',
                                                     'en' => 'View staff groups',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Группы сотрудников',
                                                     'en' => 'Staff groups',
                                                 ]),
                'policy'     => 'staff.groups.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление группами сотрудников',
                                                     'en' => 'Staff group management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Группы сотрудников',
                                                     'en' => 'Staff groups',
                                                 ]),
                'policy'     => 'staff.groups.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр списка сотрудников',
                                                     'en' => 'View the list of staff',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Сотрудники',
                                                     'en' => 'Staff',
                                                 ]),
                'policy'     => 'staff.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление сотрудниками',
                                                     'en' => 'Staff management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Сотрудники',
                                                     'en' => 'Staff',
                                                 ]),
                'policy'     => 'staff.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
