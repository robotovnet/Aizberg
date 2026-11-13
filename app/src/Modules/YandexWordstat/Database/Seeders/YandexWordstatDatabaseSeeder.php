<?php

namespace Modules\YandexWordstat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class YandexWordstatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
