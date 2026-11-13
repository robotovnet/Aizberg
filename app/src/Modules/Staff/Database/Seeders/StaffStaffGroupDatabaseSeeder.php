<?php

declare(strict_types=1);

namespace Modules\Staff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class StaffStaffGroupDatabaseSeeder
 * @package Modules\Staff\Database\Seeders
 */
class StaffStaffGroupDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_staff_group';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $records = [
            [
                'staff_id'       => 1,
                'staff_group_id' => 1,
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($records);
        }
    }
}
