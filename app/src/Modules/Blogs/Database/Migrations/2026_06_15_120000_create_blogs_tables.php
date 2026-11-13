<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Таблицы блога: разделы, статьи, теги, комментарии, права.
 */
class CreateBlogsTables extends Migration
{
    public function up(): void
    {
        Schema::create('blog_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('blog_sections')->onDelete('set null');
            $table->index('parent_id');
        });

        Schema::create('blog_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('section_id');
            $table->string('title', 255);
            $table->string('preview_image_path', 512)->nullable();
            $table->text('short_description');
            $table->longText('content');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('section_id')->references('id')->on('blog_sections');
            $table->index(['section_id', 'is_archived', 'deleted_at']);
            $table->index(['client_id', 'deleted_at']);
        });

        Schema::create('blog_article_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('blog_article_id');
            $table->string('tag', 100);

            $table->foreign('blog_article_id')->references('id')->on('blog_articles')->onDelete('cascade');
            $table->index('blog_article_id');
        });

        Schema::create('blog_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('blog_article_id');
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('blog_article_id')->references('id')->on('blog_articles')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('parent_id')->references('id')->on('blog_comments')->onDelete('cascade');
            $table->index(['blog_article_id', 'parent_id']);
        });

        Schema::create('blog_section_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id')->unique();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('blog_moderator_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id')->unique();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_moderator_permissions');
        Schema::dropIfExists('blog_section_permissions');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_article_tags');
        Schema::dropIfExists('blog_articles');
        Schema::dropIfExists('blog_sections');
    }
}
