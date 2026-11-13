<?php

declare(strict_types=1);

namespace Modules\RealProjects\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Class RealProjectsDatabaseSeeder
 * @package Modules\RealProjects\Database\Seeders
 */
class RealProjectsDatabaseSeeder extends Seeder
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
