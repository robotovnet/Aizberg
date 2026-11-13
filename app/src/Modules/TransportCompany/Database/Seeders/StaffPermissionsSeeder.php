<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Schema;
use Illuminate\Support\Str;

/**
 * Class StaffPermissionsSeeder
 * @package Modules\TransportCompany\Database\Seeders
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
                                                     'ru' => 'Просмотр транспортных компаний',
                                                     'en' => 'View transport companies',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Доставка',
                                                     'en' => 'Delivery',
                                                 ]),
                'policy'     => 'transportcompany.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Управление транспортными компаниями',
                                                     'en' => 'Transport company management',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Доставка',
                                                     'en' => 'Delivery',
                                                 ]),
                'policy'     => 'transportcompany.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр складов',
                                                     'en' => 'View warehouses',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Склады',
                                                     'en' => 'Warehouses',
                                                 ]),
                'policy'     => 'transportcompany.warehouses.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Редактирование складов',
                                                     'en' => 'Edit warehouses',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Склады',
                                                     'en' => 'Warehouses',
                                                 ]),
                'policy'     => 'transportcompany.warehouses.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Редактирование методов оплаты',
                                                     'en' => 'Managing Payment Methods',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Методы оплаты',
                                                     'en' => 'Payment methods',
                                                 ]),
                'policy'     => 'paymentmethods.management',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            [
                'name'       => Str::getLangJson([
                                                     'ru' => 'Просмотр методов оплаты',
                                                     'en' => 'View Payment Methods',
                                                 ]),
                'group_name' => Str::getLangJson([
                                                     'ru' => 'Методы оплаты',
                                                     'en' => 'Payment methods',
                                                 ]),
                'policy'     => 'paymentmethods.view',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];

        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->insert($permissions);
        }
    }
}
