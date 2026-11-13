<?php

declare(strict_types=1);

namespace Modules\PriceType\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\PriceType\Database\Seeders
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
                                                     'ru' => 'Просмотр типов цен',
                                                     'en' => 'View price types',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Цены',
                                                     'en' => 'Prices',
                                                 ]),
                'policy'     => 'pricetype.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление типами цен',
                                                     'en' => 'Managing price types',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Цены',
                                                     'en' => 'Prices',
                                                 ]),
                'policy'     => 'pricetype.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
