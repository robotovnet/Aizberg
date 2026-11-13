<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TransportCompanyDatabaseSeeder
 * @package Modules\TransportCompany\Database\Seeders
 */
class TransportCompanyDatabaseSeeder extends Seeder
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
