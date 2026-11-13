<?php

declare(strict_types=1);

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SettingsDatabaseSeeder
 * @package Modules\Settings\Database\Seeders
 */
class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
