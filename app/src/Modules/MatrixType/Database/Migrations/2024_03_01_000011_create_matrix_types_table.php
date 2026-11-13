<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrix_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('margin_up_to_BP', 20);
            $table->string('EXW_price', 20);
            $table->string('weight_in_kg', 20);
            $table->string('MRP_markup', 20);
            $table->string('RRP_markup', 20);
            $table->string('margin_up_to_P2', 20);
            $table->string('margin_up_to_P3', 20);
            $table->string('margin_up_to_P4', 20);
            $table->string('mark_up_contract', 20);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrix_types');
    }
}; 