<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePostamatsTable
 */
class CreatePostamatsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'postamats';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('tc_id', 40)->nullable();
            $table->string('name', 100)->index();
            $table->text('address');
            $table->text('address_comment')->nullable();
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('city_id')->index();
            $table->char('payment_by_card', 1);
            $table->char('payment_by_cash', 1);
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('metro_station', 100)->nullable();
            $table->unsignedInteger('max_width')->nullable();
            $table->unsignedInteger('max_height')->nullable();
            $table->unsignedInteger('max_depth')->nullable();
            $table->string('work_time', 200)->nullable();
            $table->string('break_work_time', 200)->nullable();
            $table->decimal('lat', 10, 8, false);
            $table->decimal('lng', 11, 8, false);
            $table->string('rates', 80)->nullable();
            $table->enum('adding_method', ['preset', 'manual']);
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
