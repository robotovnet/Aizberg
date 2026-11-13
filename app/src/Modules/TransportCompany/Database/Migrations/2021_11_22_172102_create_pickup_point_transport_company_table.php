<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePickupPointTransportCompanyTable
 */
class CreatePickupPointTransportCompanyTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'pickup_point_transport_company';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pickup_point_id');
            $table->unsignedInteger('transport_company_id');
            $table->unique(['transport_company_id', 'pickup_point_id'], 'pickup_transport_unique');
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
