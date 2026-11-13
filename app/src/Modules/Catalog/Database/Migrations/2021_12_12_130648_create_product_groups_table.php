<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductGroupsTable
 */
class CreateProductGroupsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_groups';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('plural_name')->unique();
            $table->text('singular_name')->nullable()->unique();
            $table->text('overview_text');
            $table->char('replacement_only', 1)->default('N');
            $table->char('hidden_for_service', 1)->default('N');
            $table->text('where_sn_desc')->nullable();
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
