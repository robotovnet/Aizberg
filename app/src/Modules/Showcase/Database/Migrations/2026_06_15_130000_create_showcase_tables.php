<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Каталог примеров решений на CMS: категории, примеры, скриншоты, модули, права модераторов.
 */
class CreateShowcaseTables extends Migration
{
    public function up(): void
    {
        Schema::create('showcase_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('showcase_examples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('category_id');
            $table->string('website_url', 512);
            $table->string('name', 255);
            $table->text('short_description');
            $table->string('moderation_status', 20)->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('approved_by_client_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('category_id')->references('id')->on('showcase_categories');
            $table->foreign('approved_by_client_id')->references('id')->on('clients');
            $table->index(['moderation_status', 'deleted_at']);
            $table->index(['category_id', 'moderation_status', 'deleted_at']);
            $table->index(['client_id', 'deleted_at']);
        });

        Schema::create('showcase_example_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('showcase_example_id');
            $table->string('image_path', 512);
            $table->unsignedInteger('sort_order')->default(0);

            $table->foreign('showcase_example_id')->references('id')->on('showcase_examples')->onDelete('cascade');
            $table->index(['showcase_example_id', 'sort_order']);
        });

        Schema::create('showcase_example_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('showcase_example_id');
            $table->string('module_name', 100);

            $table->foreign('showcase_example_id')->references('id')->on('showcase_examples')->onDelete('cascade');
            $table->index(['module_name', 'showcase_example_id']);
        });

        Schema::create('showcase_moderator_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id')->unique();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        $now = now();
        DB::table('showcase_categories')->insert([
            [
                'name' => 'Интернет-магазин',
                'slug' => 'online-store',
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Блог',
                'slug' => 'blog',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Корпоративный сайт',
                'slug' => 'corporate',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Лендинг',
                'slug' => 'landing',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Сервисный центр',
                'slug' => 'service-center',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Другое',
                'slug' => 'other',
                'sort_order' => 99,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('showcase_moderator_permissions');
        Schema::dropIfExists('showcase_example_modules');
        Schema::dropIfExists('showcase_example_images');
        Schema::dropIfExists('showcase_examples');
        Schema::dropIfExists('showcase_categories');
    }
}
