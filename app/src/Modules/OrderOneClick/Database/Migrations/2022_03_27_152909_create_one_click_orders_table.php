<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateOneClickOrdersTable
 */
class CreateOneClickOrdersTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'one_click_orders';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->char('is_completed', 1)->index();
            $table->unsignedInteger('product_id');
            $table->string('client_phone', 60)->index();
            $table->string('client_email', 60)->index();
            $table->softDeletes();
            $table->timestamps();
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
