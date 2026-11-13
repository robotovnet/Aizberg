<?php

declare(strict_types=1);

namespace Modules\MatrixType\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\MatrixType\Database\Seeders
 */
class StaffPermissionsSeeder extends Seeder
{
    /** @var string Table name. */
    private string $table = 'staff_permissions';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $permissions = [
            [
                'name'       => Str::getLangJson([
                    'ru' => 'Возможность создавать и редактировать типы матриц',
                    'en' => 'The ability to create and edit matrix types',
                    ]),
                'group_name' => Str::getLangJson([
                    'ru' => 'Типы матриц',
                    'en' => 'Matrix Types',
                    ]),
                'policy'     => 'matrixtype.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                    'ru' => 'Просмотр типов матриц',
                    'en' => 'Viewing matrix types',
                    ]),
                'group_name' => Str::getLangJson([
                    'ru' => 'Типы матриц',
                    'en' => 'Matrix Types',
                    ]),
                'policy'     => 'matrixtype.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
