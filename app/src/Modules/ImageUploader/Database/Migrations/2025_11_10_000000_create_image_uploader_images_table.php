<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Preserve images on installations where the table already exists
        // but this migration has not been recorded yet.
        if (Schema::hasTable('image_uploader')) {
            return;
        }

        Schema::create('image_uploader', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('original_path');
            $table->string('micro_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_uploader');
    }
};

