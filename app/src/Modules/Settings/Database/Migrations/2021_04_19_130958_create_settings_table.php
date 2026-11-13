<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSettingsTable
 */
class CreateSettingsTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'settings';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order');
            $table->string('code', 30)->unique();
            $table->text('name');
            $table->string('value', 255)->nullable();
            $table->string('section_code', 100);
            $table->text('placeholder')->nullable();
            $table->text('items')->nullable();
            $table->enum('type_form_element', ['radio', 'checkbox', 'input', 'select']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
}
