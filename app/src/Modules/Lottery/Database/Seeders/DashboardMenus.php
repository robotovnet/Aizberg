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
 * Class DashboardMenus
 * @package Modules\Lottery\Database\Seeders
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
                                    'ru' => 'Лотереи',
                                    'en' => 'Lottery',
                                ]),
                'code'         => '2_lottery',
                'href'         => null,
                'parent'       => '1_settings',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 2,
                'policy'       => null,
                'order'        => '8000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                    'ru' => 'Результаты',
                                    'en' => 'Results',
                                ]),
                'code'         => '3_lottery_results',
                'href'         => '/dashboard/lottery',
                'parent'       => '2_lottery',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'lottery.view',
                'order'        => '1000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                    'ru' => 'Призы',
                                    'en' => 'Prizes',
                                ]),
                'code'         => '3_lottery_prize',
                'href'         => '/dashboard/lottery/prize',
                'parent'       => '2_lottery',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'lottery.management',
                'order'        => '2000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                    'ru' => 'Заявки',
                                    'en' => 'Applications',
                                ]),
                'code'         => '3_lottery_apps',
                'href'         => '/dashboard/lottery/apps',
                'parent'       => '2_lottery',
                'image_src'    => null,
                'image_inline' => null,
                'level'        => 3,
                'policy'       => 'lottery.view',
                'order'        => '3000',
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
