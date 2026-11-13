<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShippingCostsTable
 */
class CreateShippingCostsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'shipping_costs';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('transport_company_id')->nullable();
            $table->text('rates');
            $table->char('is_support_courier_delivery', 1)->default('N');
            $table->char('is_support_postamats', 1)->default('N');
            $table->char('is_support_pickup_points', 1)->default('N');
            $table->timestamps();
            $table->softDeletes();
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
