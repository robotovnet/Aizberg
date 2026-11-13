<?php

declare(strict_types=1);

namespace Modules\RealProjects\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Пункт «Настройки → Справочники → В проектах».
 * @package Modules\RealProjects\Database\Seeders
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

        $item = [
            'name'         => Str::getLangJson([
                'ru' => 'В проектах',
                'en' => 'In projects',
            ]),
            'code'         => '3_real_projects',
            'href'         => '/dashboard/real-projects',
            'parent'       => '2_directories',
            'image_src'    => null,
            'image_inline' => null,
            'level'        => 3,
            'policy'       => 'realprojects.view',
            'order'        => '21000',
            'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        if (!Schema::hasTable($this->table)) {
            return;
        }

        // Повторный запуск module:seed не должен дублировать пункт меню
        if (!DB::table($this->table)->where('code', $item['code'])->whereNull('deleted_at')->exists()) {
            DB::table($this->table)->insert($item);
        }
    }
}
