<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFeatureProductTable
 */
class CreateFeatureProductTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'feature_product';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('product_id')->index();
            $table->unsignedInteger('feature_id')->index();
            $table->string('value', 255);
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
