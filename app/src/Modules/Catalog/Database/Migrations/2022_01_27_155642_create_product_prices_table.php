<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductPricesTable
 */
class CreateProductPricesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_prices';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('price_type_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('value');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['product_id', 'price_type_id']);
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
