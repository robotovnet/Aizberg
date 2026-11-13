<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWarehousesTable
 */
class CreateWarehousesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'warehouses';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(
            $this->table,
            function (Blueprint $table) {
                $table->increments('id');
                $table->text('name');
                $table->string('label_1c', 30);
                $table->string('code', 30)->unique();
                $table->unsignedInteger('priority');
                $table->char('is_active', 1)->default('N');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
