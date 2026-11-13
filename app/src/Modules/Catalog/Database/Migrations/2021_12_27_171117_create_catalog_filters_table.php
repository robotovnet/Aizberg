<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCatalogFiltersTable
 */
class CreateCatalogFiltersTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'catalog_filters';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_group_id')->index();
            $table->unsignedInteger('feature_id')->index();
            $table->unsignedInteger('order');
            $table->enum('type', ['radio', 'checkbox', 'range', 'input_with_checkbox']);
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
