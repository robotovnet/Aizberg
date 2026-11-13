<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLotteryAppsTable
 */
class CreateLotteryPrizesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'lottery_prizes';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('product_id');
            $table->enum('status', ['playing','raffled_off'])->default('playing');
            $table->unsignedSmallInteger('week');
            $table->unsignedSmallInteger('year');
            
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
