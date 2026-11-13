<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductsTable
 */
class CreateProductsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'products';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->index();
            $table->string('ext_name', 255)->nullable();
            $table->string('vendor_code', 255)->nullable()->index();
            $table->string('uuid', 36)->unique();
            $table->unsignedInteger('brand_id')->index();
            $table->unsignedInteger('product_group_id')->nullable()->index();
            $table->char('is_hidden', 1);
            $table->char('not_for_everyone', 1)->nullable();
            $table->text('overview_text')->nullable();
            $table->enum('stock_status', ['in_stock', 'pre_order', 'out_of_stock'])->index();
            $table->unsignedInteger('views_counter')->nullable();
            $table->char('has_photos', 1)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->fullText(['name', 'vendor_code', 'overview_text'], 'products_search_fulltext');
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
