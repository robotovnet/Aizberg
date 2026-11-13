<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Таблицы маркетплейса модулей.
 */
class CreateMarketplaceModulesTables extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id');
            $table->string('display_name', 255);
            $table->string('install_slug', 255)->unique();
            $table->string('website_url', 1024);
            $table->string('source_code_url', 1024);
            $table->string('license_type', 64);
            $table->string('license_url', 1024)->nullable();
            $table->string('changelog_url', 1024)->nullable();
            $table->string('pricing_type', 32);
            $table->string('author_display_name', 255);
            $table->text('short_description');
            $table->string('category', 30);
            $table->longText('detailed_description')->nullable();
            $table->string('icon_path', 512)->nullable();
            $table->unsignedInteger('ratings_count')->default(0);
            $table->decimal('aggregate_rating', 3, 2)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->index(['category', 'is_archived', 'deleted_at']);
            $table->index(['is_archived', 'deleted_at']);
        });

        Schema::create('marketplace_module_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->string('tag', 100);
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_tags_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->index('tag');
        });

        Schema::create('marketplace_module_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->string('version', 64);
            $table->dateTime('release_date');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_versions_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->index(['marketplace_module_id', 'release_date'], 'mm_versions_module_release_idx');
        });

        Schema::create('marketplace_module_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->unsignedInteger('client_id');
            $table->text('question_text');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_questions_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->index(['marketplace_module_id', 'created_at'], 'mm_questions_module_created_idx');
        });

        Schema::create('marketplace_module_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_question_id');
            $table->unsignedInteger('client_id');
            $table->text('answer_text');
            $table->timestamps();

            $table->foreign('marketplace_module_question_id', 'mm_answers_question_fk')
                ->references('id')->on('marketplace_module_questions')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->index('marketplace_module_question_id');
        });

        Schema::create('marketplace_module_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->unsignedInteger('client_id');
            $table->longText('review_text');
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_reviews_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unique(['marketplace_module_id', 'client_id'], 'mm_reviews_module_client_unique');
            $table->index(['marketplace_module_id', 'updated_at'], 'mm_reviews_module_updated_idx');
        });

        Schema::create('marketplace_module_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('marketplace_module_id');
            $table->unsignedInteger('client_id');
            $table->string('value', 16);
            $table->timestamps();

            $table->foreign('marketplace_module_id', 'mm_ratings_module_fk')
                ->references('id')->on('marketplace_modules')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unique(['marketplace_module_id', 'client_id'], 'mm_ratings_module_client_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_module_ratings');
        Schema::dropIfExists('marketplace_module_reviews');
        Schema::dropIfExists('marketplace_module_answers');
        Schema::dropIfExists('marketplace_module_questions');
        Schema::dropIfExists('marketplace_module_versions');
        Schema::dropIfExists('marketplace_module_tags');
        Schema::dropIfExists('marketplace_modules');
    }
}
