<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductAdGeneratorTable
 */
class CreateProductAdGeneratorTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_ad_generator';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->index();
            $table->text('alt_names');
            $table->text('pn');
            $table->timestamps();
            $table->softDeletes();
            $table->fullText(['alt_names', 'pn'], 'product_ad_generator_search_fulltext');
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
