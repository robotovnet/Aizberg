<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yandex_wordstats', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code', 99);
            $table->string('phrase', 255);
            $table->enum('type', ['pn', 'model']);
            $table->unsignedInteger('total');
            $table->text('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yandex_wordstats');
    }
}; 