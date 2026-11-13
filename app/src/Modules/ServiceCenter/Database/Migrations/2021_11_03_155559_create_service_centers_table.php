<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateServiceCentersTable
 */
class CreateServiceCentersTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'service_centers';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->index();
            $table->text('address')->index();
            $table->text('address_comment')->nullable();
            $table->text('site')->nullable();
            $table->unsignedInteger('country_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('metro_station', 100)->nullable();
            $table->string('work_time', 200)->nullable();
            $table->string('break_work_time', 200)->nullable();
            $table->decimal('lat', 10, 8, false);
            $table->decimal('lng', 11, 8, false);
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
