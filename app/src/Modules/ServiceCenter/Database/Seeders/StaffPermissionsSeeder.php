<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\ServiceCenter\Database\Seeders
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
                                                     'ru' => 'Просмотр сервисных центров',
                                                     'en' => 'View service centers',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Сервисные центры',
                                                     'en' => 'Service centers',
                                                 ]),
                'policy'     => 'servicecenter.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление сервисными центрами',
                                                     'en' => 'Service centers management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Сервисные центры',
                                                     'en' => 'Service centers',
                                                 ]),
                'policy'     => 'servicecenter.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
