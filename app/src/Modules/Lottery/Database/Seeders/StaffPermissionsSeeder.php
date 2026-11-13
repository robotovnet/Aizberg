<?php

declare(strict_types=1);

namespace Modules\Lottery\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\Lottery\Database\Seeders
 */
class StaffPermissionsSeeder extends Seeder
{
    private const TABLE = 'staff_permissions';
    
    private const PERMISSIONS = [
        [
            'name' => [
                'ru' => 'Управление розыгрышами',
                'en' => 'Managing lottery',
            ],
            'group_name' => [
                'ru' => 'Розыгрыши',
                'en' => 'Lottery',
            ],
            'policy' => 'lottery.management',
        ],
        [
            'name' => [
                'ru' => 'Просмотр результатов лотерей',
                'en' => 'Viewing lottery results',
            ],
            'group_name' => [
                'ru' => 'Розыгрыши',
                'en' => 'Lottery',
            ],
            'policy' => 'lottery.view',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        $permissions = array_map(function ($permission) {
            return [
                'name' => Str::getLangJson($permission['name']),
                'group_name' => Str::getLangJson($permission['group_name']),
                'policy' => $permission['policy'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
        }, self::PERMISSIONS);

        DB::table(self::TABLE)->insert($permissions);
    }
}
