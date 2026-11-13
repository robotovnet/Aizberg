<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CatalogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(DashboardMenus::class);
        $this->call(StaffPermissionsSeeder::class);
        $this->call(SettingsSectionsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }
}
