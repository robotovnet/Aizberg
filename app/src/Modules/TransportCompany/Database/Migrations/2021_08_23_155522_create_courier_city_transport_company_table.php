<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCourierCityTransportCompanyTable
 */
class CreateCourierCityTransportCompanyTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'courier_city_transport_company';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('courier_city_id')->index();
            $table->unsignedInteger('transport_company_id')->index();
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
