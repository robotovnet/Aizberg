<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientUserpicsTable extends Migration
{
    public function up(): void
    {
        // Some installations received this table before the migration was
        // registered. Keep the existing userpics and allow Laravel to record
        // this migration as completed.
        if (Schema::hasTable('client_userpics')) {
            return;
        }

        Schema::create('client_userpics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id')->unique();
            $table->string('path', 512);
            $table->string('original_name', 255);
            $table->string('mime_type', 100);
            $table->unsignedInteger('size');
            $table->timestamps();

            $table->foreign('client_id', 'client_userpics_client_fk')
                ->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_userpics');
    }
}
