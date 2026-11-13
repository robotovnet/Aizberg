<?php

declare(strict_types=1);

namespace Modules\Staff\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * Class StaffGroupsDatabaseSeeder
 * @package Modules\Staff\Database\Seeders
 */
class StaffGroupsDatabaseSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_groups';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $records = [
            [
                'name'       => 'Super users',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($records);
        }
    }
}
