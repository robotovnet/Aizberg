<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCurrenciesTable
 */
class CreateCurrenciesTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'currencies';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->char('code', 3);
            $table->string('symbol', 5);
            $table->char('country_code', 2);
            $table->char('is_default', 1)->default('N');
            $table->float('extra_charge')->default(0.00);
            $table->timestamps();
            $table->softDeletes();
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
