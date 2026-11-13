<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostamatTransportCompanyTable extends Migration
{
    /** @var string Table name. */
    private string $table = 'postamat_transport_company';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('postamat_id');
            $table->unsignedInteger('transport_company_id');
            $table->index(['transport_company_id', 'postamat_id'], 'postamat_transport_company_idx');
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
