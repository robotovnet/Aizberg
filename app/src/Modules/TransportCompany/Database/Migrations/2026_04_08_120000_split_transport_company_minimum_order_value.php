<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Разделяет минимальную сумму заказа ТК на курьерскую и ПВЗ/постаматы.
 */
return new class extends Migration
{
    private string $table = 'transport_companies';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->unsignedInteger('minimum_order_value_courier')->default(0)->after('accept_orders_completion_time');
            $table->unsignedInteger('minimum_order_value_pickup')->default(0)->after('minimum_order_value_courier');
        });

        if (Schema::hasColumn($this->table, 'minimum_order_value')) {
            DB::statement(
                'UPDATE transport_companies SET minimum_order_value_courier = minimum_order_value, '
                . 'minimum_order_value_pickup = minimum_order_value'
            );

            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('minimum_order_value');
            });
        }
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->unsignedInteger('minimum_order_value')->default(0)->after('accept_orders_completion_time');
        });

        DB::statement(
            'UPDATE transport_companies SET minimum_order_value = '
            . 'CASE WHEN minimum_order_value_courier >= minimum_order_value_pickup '
            . 'THEN minimum_order_value_courier ELSE minimum_order_value_pickup END'
        );

        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn(['minimum_order_value_courier', 'minimum_order_value_pickup']);
        });
    }
};
