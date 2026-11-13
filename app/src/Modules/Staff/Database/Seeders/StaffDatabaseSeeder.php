<?php

declare(strict_types=1);

namespace Modules\Staff\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;

/**
 * Class StaffDatabaseSeeder
 * @package Modules\Staff\Database\Seeders
 */
class StaffDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        if (Helper::isNonProductEnv()) {
            DB::table($this->table)->insert([
                'first_name'   => 'Super',
                'last_name'    => 'User',
                'phone'        => '79251111111',
                'email'        => 'root@aizberg.ru',
                'password'     => Hash::make('password'),
                'created_at'   => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'   => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        $this->call(StaffPermissionsSeeder::class);
        $this->call(StaffGroupsDatabaseSeeder::class);
        $this->call(StaffStaffGroupDatabaseSeeder::class);
        $this->call(StaffGroupStaffPermissionSeeder::class);
        $this->call(DashboardMenus::class);
    }
}
