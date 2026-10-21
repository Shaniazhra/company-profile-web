<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Zoom and position for each gallery photo. Safe to run twice: every column is checked first. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_photos', function (Blueprint $table) {
            if (! Schema::hasColumn('about_photos', 'zoom')) {
                $table->decimal('zoom', 3, 2)->default(1);                 // 1.00 to 3.00
            }
            if (! Schema::hasColumn('about_photos', 'focus_x')) {
                $table->unsignedTinyInteger('focus_x')->default(50);       // 0 = left, 100 = right
            }
            if (! Schema::hasColumn('about_photos', 'focus_y')) {
                $table->unsignedTinyInteger('focus_y')->default(50);       // 0 = top, 100 = bottom
            }
        });
    }

    public function down(): void
    {
        $columns = array_filter(['zoom', 'focus_x', 'focus_y'], fn ($c) => Schema::hasColumn('about_photos', $c));

        if ($columns) {
            Schema::table('about_photos', fn (Blueprint $table) => $table->dropColumn(array_values($columns)));
        }
    }
};