<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDeliverySettingsTable
 */
class CreateDeliverySettingsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'delivery_settings';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('days_transfer');
            $table->string('transfer_time_tomorrow', 5)->nullable();
            $table->string('transfer_time_after_tomorrow', 5)->nullable();
            $table->string('transport_company_working_days', 36)->nullable();
            $table->string('transport_company_weekends', 36)->nullable();
            $table->unsignedInteger('transport_company_id')->index();
            $table->enum('delivery_type', ['postamat', 'courier_city', 'pickup_point'])->index();
            $table->unsignedInteger('delivery_point_id')->index()->nullable();
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
