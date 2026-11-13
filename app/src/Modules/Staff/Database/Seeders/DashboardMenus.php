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
 * Class DashboardMenus
 * @package Modules\Staff\Database\Seeders
 */
class DashboardMenus extends Seeder
{
    /** @var string Table name. */
    private string $table = 'dashboard_menus';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $permissions = [
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Группы сотрудников',
                                                     'en' => 'Staff Groups',
                                                 ]),
                'code'         => '3_staff_groups',
                'href'         => '/dashboard/staff-groups',
                'parent'       => '2_users',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'staff.groups.view',
                'order'        => '800000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Сотрудники',
                                                     'en' => 'Staff',
                                                 ]),
                'code'         => '3_staff',
                'href'         => '/dashboard/staff',
                'parent'       => '2_users',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'staff.view',
                'order'        => '700000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
