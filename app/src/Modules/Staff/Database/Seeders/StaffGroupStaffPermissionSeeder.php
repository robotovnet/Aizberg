<?php

declare(strict_types=1);

namespace Modules\Staff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class StaffGroupStaffPermissionSeeder
 * @package Modules\Staff\Database\Seeders
 */
class StaffGroupStaffPermissionSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_group_staff_permission';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $records = [];

        for ($i = 1; $i < 60; $i++) {
            $records[($i - 1)]['staff_group_id'] = 1;
            $records[($i - 1)]['staff_permission_id'] = $i;
        }

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($records);
        }
    }
}
