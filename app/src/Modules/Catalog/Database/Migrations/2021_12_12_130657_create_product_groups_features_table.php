<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProductGroupsFeaturesTable
 */
class CreateProductGroupsFeaturesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'product_groups_features';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->index();
            $table->unsignedInteger('feature_id')->index();
            $table->string('default_value', 100)->nullable();
            $table->text('overview_text')->nullable();
            $table->char('is_required', 1);
            $table->char('is_main', 1);
            $table->unsignedInteger('order');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['feature_id', 'group_id']);
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
