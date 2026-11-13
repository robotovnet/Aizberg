<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTransportCompanyWarehouseTable
 */
class CreateTransportCompanyWarehouseTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'transport_company_warehouse';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id')->index();
            $table->unsignedInteger('transport_company_id')->index();
            $table->char('for_all_courier_cities', 1);
            $table->char('for_all_postamats', 1);
            $table->char('for_all_pickup_points', 1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
