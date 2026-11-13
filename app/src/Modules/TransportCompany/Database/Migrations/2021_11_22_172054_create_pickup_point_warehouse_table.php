<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePickupPointWarehouseTable
 */
class CreatePickupPointWarehouseTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'pickup_point_warehouse';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pickup_point_id')->index();
            $table->unsignedInteger('warehouse_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
}
