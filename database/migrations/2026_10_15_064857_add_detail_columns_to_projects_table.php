<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel belum ada: buat lengkap.
        if (! Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->json('title');
                $table->json('tag')->nullable();
                $table->json('synopsis')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->string('image')->nullable();
                $table->string('youtube_url')->nullable();
                $table->unsignedSmallInteger('release_year')->nullable();
                $table->boolean('is_published')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->json('characters')->nullable();
                $table->timestamps();
            });

            return;
        }

        // Tabel sudah ada: tambah hanya kolom yang belum ada.
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'tag'))          $table->json('tag')->nullable();
            if (! Schema::hasColumn('projects', 'synopsis'))     $table->json('synopsis')->nullable();
            if (! Schema::hasColumn('projects', 'slug'))         $table->string('slug')->nullable();
            if (! Schema::hasColumn('projects', 'image'))        $table->string('image')->nullable();
            if (! Schema::hasColumn('projects', 'youtube_url'))  $table->string('youtube_url')->nullable();
            if (! Schema::hasColumn('projects', 'release_year')) $table->unsignedSmallInteger('release_year')->nullable();
            if (! Schema::hasColumn('projects', 'is_published')) $table->boolean('is_published')->default(false);
            if (! Schema::hasColumn('projects', 'sort_order'))   $table->unsignedInteger('sort_order')->default(0);
            if (! Schema::hasColumn('projects', 'characters'))   $table->json('characters')->nullable();
        });

        if (! Schema::hasIndex('projects', 'projects_slug_unique')) {
            Schema::table('projects', fn (Blueprint $table) => $table->unique('slug'));
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            foreach (['synopsis', 'slug', 'image', 'youtube_url', 'sort_order', 'characters'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    if ($column === 'slug' && Schema::hasIndex('projects', 'projects_slug_unique')) {
                        $table->dropUnique('projects_slug_unique');
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};