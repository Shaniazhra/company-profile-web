<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Every project gets a slug (older rows may have none), built from the old title before it is removed.
        if (Schema::hasColumn('projects', 'title')) {
            foreach (DB::table('projects')->get(['id', 'title', 'slug']) as $row) {
                if (filled($row->slug)) {
                    continue;
                }

                $title = json_decode($row->title ?? '', true) ?: [];
                $base  = Str::slug($title['en'] ?? $title['id'] ?? '') ?: 'project-'.$row->id;
                $slug  = $base;
                $i     = 2;

                while (DB::table('projects')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }

                DB::table('projects')->where('id', $row->id)->update(['slug' => $slug]);
            }
        }

        // 2. The form only edits English. Copy the Indonesian text into English wherever English is empty.
        foreach (['tag', 'synopsis'] as $column) {
            if (! Schema::hasColumn('projects', $column)) {
                continue;
            }

            foreach (DB::table('projects')->get(['id', $column]) as $row) {
                $value = json_decode($row->{$column} ?? '', true);

                if (! is_array($value)) {
                    continue;
                }

                if (blank($value['en'] ?? null) && filled($value['id'] ?? null)) {
                    $value['en'] = $value['id'];

                    DB::table('projects')->where('id', $row->id)->update([
                        $column => json_encode($value, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }
        }

        // 3. The form has no title field any more.
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'title')) {
                $table->dropColumn('title');
            }
        });
    }

    public function down(): void
    {
        // The column comes back empty. Restore the old titles from the backup file if needed.
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'title')) {
                $table->json('title')->nullable();
            }
        });
    }
};