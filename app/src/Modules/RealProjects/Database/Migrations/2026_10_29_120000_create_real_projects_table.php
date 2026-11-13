<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url', 2048);
            // Путь к баннеру относительно диска (например, real-projects/abc.svg)
            $table->string('banner_path');
            $table->unsignedInteger('sort')->default(500)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_projects');
    }
};
