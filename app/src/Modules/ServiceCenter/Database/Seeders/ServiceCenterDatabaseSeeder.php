<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ServiceCenterDatabaseSeeder
 * @package Modules\ServiceCenter\Database\Seeders
 */
class ServiceCenterDatabaseSeeder extends Seeder
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
