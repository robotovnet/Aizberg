<?php

declare(strict_types=1);

namespace Modules\Qa\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\Qa\Database\Seeders
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
                                                     'ru' => 'Возможность отвечать на вопросы клиентов',
                                                     'en' => 'The ability to answer customer questions',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Вопросы/ответы',
                                                     'en' => 'Questions/Answers',
                                                 ]),
                'policy'     => 'qa.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр вопросов/ответов',
                                                     'en' => 'Viewing questions/answers',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Вопросы/ответы',
                                                     'en' => 'Questions/Answers',
                                                 ]),
                'policy'     => 'qa.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
