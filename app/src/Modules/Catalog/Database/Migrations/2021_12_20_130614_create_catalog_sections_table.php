<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCatalogSectionsTable
 */
class CreateCatalogSectionsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'catalog_sections';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('parent_section_id')->nullable()->index();
            $table->unsignedInteger('product_group_id')->nullable();
            $table->string('thumbnail', 60)->nullable();
            $table->char('is_hidden', 1)->default('N');
            $table->char('not_for_everyone', 1)->default('N');
            $table->char('is_active_tab_parameters', 1)->default('N');
            $table->char('is_bp_disabled', 1)->default('N');
            $table->char('should_display_on_main_page', 1)->default('N');
            $table->text('seo_starting_phrases')->nullable();
            $table->text('seo_desc')->nullable();
            $table->text('additional_text')->nullable();
            $table->text('starting_text_product_name')->nullable();
            $table->enum('sorting_method', ['by_novelty', 'by_availability', 'by_prices', 'by_alphabet', 'by_views']);
            $table->unsignedInteger('order');
            $table->unsignedInteger('tiled_order')->nullable();
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
