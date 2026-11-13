<?php

namespace Modules\BonusProgram\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BonusProgramDatabaseSeeder extends Seeder
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
        $this->call(SettingsTableSeeder::class);
        $this->call(SettingsSectionsTableSeeder::class);
    }
}
