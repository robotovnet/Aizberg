<?php

declare(strict_types=1);

namespace Modules\ClientType\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\City\Database\Seeders
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
                                                     'ru' => 'Типы клиентов',
                                                     'en' => 'Client types',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Типы клиентов',
                                                     'en' => 'Client types',
                                                 ]),
                'policy'     => 'clienttype.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление типами клиентов',
                                                     'en' => 'Managing client types',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Типы клиентов',
                                                     'en' => 'Client types',
                                                 ]),
                'policy'     => 'clienttype.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
