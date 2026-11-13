<?php

declare(strict_types=1);

namespace Modules\RealProjects\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\RealProjects\Database\Seeders
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

        $groupName = Str::getLangJson([
            'ru' => 'В реальных проектах',
            'en' => 'Real projects',
        ]);

        $permissions = [
            [
                'name'       => Str::getLangJson([
                    'ru' => 'Возможность добавлять, изменять и удалять проекты',
                    'en' => 'The ability to create, edit and delete projects',
                ]),
                'group_name' => $groupName,
                'policy'     => 'realprojects.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                    'ru' => 'Просмотр проектов',
                    'en' => 'Viewing projects',
                ]),
                'group_name' => $groupName,
                'policy'     => 'realprojects.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (!Schema::hasTable($this->table)) {
            return;
        }

        // Повторный запуск module:seed не должен дублировать права
        foreach ($permissions as $permission) {
            if (!DB::table($this->table)->where('policy', $permission['policy'])->exists()) {
                DB::table($this->table)->insert($permission);
            }
        }
    }
}
