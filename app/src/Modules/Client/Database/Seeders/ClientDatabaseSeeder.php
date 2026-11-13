<?php

declare(strict_types=1);

namespace Modules\Client\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Helpers\Helper;

/**
 * Class ClientDatabaseSeeder
 * @package Modules\Client\Database\Seeders
 */
class ClientDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'clients';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        if (Helper::isNonProductEnv()) {
            DB::table($this->table)->insert([
                 'first_name'   => 'Test',
                 'last_name'    => 'Client',
                 'phone'        => '79251112233',
                 'email'        => 'test_client@mail.com',
                 'password'     => Hash::make('password'),
                 'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                 'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        $this->call(SettingsSectionsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(StaffPermissionsSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
