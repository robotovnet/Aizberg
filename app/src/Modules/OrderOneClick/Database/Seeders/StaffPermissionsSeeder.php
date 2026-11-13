<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\OrderOneClick\Database\Seeders
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
                                                    'ru' => 'Работа с заказами',
                                                    'en' => 'Working with orders',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Заказы в 1 клик',
                                                     'en' => 'One click orders',
                                                 ]),
                'policy'     => 'orderoneclick.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление категориями и оповещениями',
                                                     'en' => 'Manage categories and notifications',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Заказы в 1 клик',
                                                     'en' => 'One click orders',
                                                 ]),
                'policy'     => 'orderoneclick.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
