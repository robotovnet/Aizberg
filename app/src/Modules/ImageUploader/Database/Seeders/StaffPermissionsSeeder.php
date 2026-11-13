<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StaffPermissionsSeeder extends Seeder
{
    private string $table = 'staff_permissions';

    public function run(): void
    {
        Model::unguard();

        if (!Schema::hasTable($this->table)) {
            return;
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $permissions = [
            [
                'name' => Str::getLangJson([
                    'ru' => 'Просмотр изображений',
                    'en' => 'Viewing images',
                ]),
                'group_name' => Str::getLangJson([
                    'ru' => 'Каталог изображений',
                    'en' => 'Image catalog',
                ]),
                'policy' => 'imageuploader.view',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => Str::getLangJson([
                    'ru' => 'Управление изображениями',
                    'en' => 'Managing images',
                ]),
                'group_name' => Str::getLangJson([
                    'ru' => 'Каталог изображений',
                    'en' => 'Image catalog',
                ]),
                'policy' => 'imageuploader.management',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table($this->table)->insert($permissions);
    }
}

