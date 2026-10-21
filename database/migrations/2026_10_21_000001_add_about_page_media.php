<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * About page media in one migration:
 *  - site_settings: video + poster, section toggles, texts for video / gallery / closing, and photo positions
 *  - about_photos: the gallery photos
 *
 * Every step checks first whether it is already there, so it is safe to run on a database
 * where one of the older separate migrations has already run.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Texts with an EN version (saved as JSON by spatie/laravel-translatable, like the other texts)
            foreach (['bts_title', 'bts_text', 'gallery_title', 'gallery_text', 'cta_title', 'cta_text'] as $column) {
                if (! Schema::hasColumn('site_settings', $column)) {
                    $table->text($column)->nullable();
                }
            }

            // Behind-the-scenes video and its poster: paths on the public disk
            foreach (['bts_video', 'bts_poster'] as $column) {
                if (! Schema::hasColumn('site_settings', $column)) {
                    $table->string($column)->nullable();
                }
            }

            // Show or hide a whole section on the About page
            foreach (['bts_enabled', 'gallery_enabled'] as $column) {
                if (! Schema::hasColumn('site_settings', $column)) {
                    $table->boolean($column)->default(true);
                }
            }

            // Position and zoom of each photo, e.g. {"about_image": {"x": 40, "y": 20, "z": 1.3}}
            if (! Schema::hasColumn('site_settings', 'image_focus')) {
                $table->json('image_focus')->nullable();
            }
        });

        if (! Schema::hasTable('about_photos')) {
            Schema::create('about_photos', function (Blueprint $table) {
                $table->id();
                $table->string('path');                              // file on the public disk
                $table->string('alt', 160)->nullable();              // alt text for screen readers
                $table->unsignedInteger('sort_order')->default(0);   // 1 = first
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_photos');

        $columns = array_filter(
            [
                'bts_title', 'bts_text', 'gallery_title', 'gallery_text', 'cta_title', 'cta_text',
                'bts_video', 'bts_poster', 'bts_enabled', 'gallery_enabled', 'image_focus',
            ],
            fn ($column) => Schema::hasColumn('site_settings', $column)
        );

        if ($columns) {
            Schema::table('site_settings', function (Blueprint $table) use ($columns) {
                $table->dropColumn(array_values($columns));
            });
        }
    }
};