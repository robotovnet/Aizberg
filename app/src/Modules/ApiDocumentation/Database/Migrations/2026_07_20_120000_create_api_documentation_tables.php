<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiDocumentationTables extends Migration
{
    public function up(): void
    {
        Schema::create('api_documentations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id');
            $table->string('slug', 255)->unique();
            $table->json('name');
            $table->json('short_description');
            $table->json('tags')->nullable();
            $table->string('default_locale', 16)->default('en');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->index(['is_published', 'deleted_at']);
        });

        Schema::create('api_documentation_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('api_documentation_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedTinyInteger('level');
            $table->unsignedInteger('position')->default(0);
            $table->string('slug', 255);
            $table->json('title');
            $table->json('description_blocks')->nullable();
            $table->string('http_method', 16)->nullable();
            $table->string('endpoint_url', 2048)->nullable();
            $table->string('request_body_content_type', 255)->nullable();
            $table->json('request_body_schema')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_parameters')->nullable();
            $table->json('request_example')->nullable();
            $table->json('responses')->nullable();
            $table->timestamps();

            $table->foreign('api_documentation_id', 'api_doc_sections_documentation_fk')
                ->references('id')->on('api_documentations')->onDelete('cascade');
            $table->foreign('parent_id', 'api_doc_sections_parent_fk')
                ->references('id')->on('api_documentation_sections')->onDelete('cascade');
            $table->unique(['api_documentation_id', 'slug'], 'api_doc_sections_documentation_slug_unique');
            $table->index(['api_documentation_id', 'parent_id', 'position'], 'api_doc_sections_tree_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_documentation_sections');
        Schema::dropIfExists('api_documentations');
    }
}
