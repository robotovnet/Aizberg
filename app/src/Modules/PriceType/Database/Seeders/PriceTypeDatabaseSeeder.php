<?php

declare(strict_types=1);

namespace Modules\PriceType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceTypeDatabaseSeeder
 * @package Modules\PriceType\Database\Seeders
 */
class PriceTypeDatabaseSeeder extends Seeder
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
