<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Position (x and y in percent) and zoom (1 = no zoom) of each team photo inside its card. Defaults keep the current look: centered, at the bottom. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_members', function (Blueprint $table) {
            if (! Schema::hasColumn('crew_members', 'photo_x')) {
                $table->decimal('photo_x', 5, 2)->default(50);
            }
            if (! Schema::hasColumn('crew_members', 'photo_y')) {
                $table->decimal('photo_y', 5, 2)->default(100);
            }
            if (! Schema::hasColumn('crew_members', 'photo_zoom')) {
                $table->decimal('photo_zoom', 4, 2)->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('crew_members', function (Blueprint $table) {
            foreach (['photo_x', 'photo_y', 'photo_zoom'] as $column) {
                if (Schema::hasColumn('crew_members', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};