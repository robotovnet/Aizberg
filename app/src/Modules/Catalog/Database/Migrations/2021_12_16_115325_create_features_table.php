<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFeaturesTable
 */
class CreateFeaturesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'features';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->index();
            $table->text('unit')->nullable();
            $table->enum('sort_type', ['numerical', 'string']);
            $table->char('is_for_all_groups', 1)->default('N');
            $table->enum('type', ['only_input', 'radio', 'checkbox']);
            $table->string('value_separator', 10)->nullable()->default(', ');
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
