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
 * Class DashboardMenus
 * @package Modules\ClientType\Database\Seeders
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
                'name'          => Str::getLangJson([
                                   'ru' => 'Типы клиентов',
                                   'en' => 'Client types',
                                ]),
                'code'         => '3_client_types',
                'href'         => '/dashboard/client-types',
                'parent'       => '2_users',
                'image_src'    => null,
                'image_inline' => null,
                'level'         => 3,
                'policy'       => 'clienttype.view',
                'order'        => '200000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
