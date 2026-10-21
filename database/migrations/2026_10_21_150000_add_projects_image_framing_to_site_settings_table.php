<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Position and zoom of the Projects page hero photo (x and y in percent, zoom 1 = no zoom) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'projects_image_x')) {
                $table->decimal('projects_image_x', 5, 2)->default(50);
            }
            if (! Schema::hasColumn('site_settings', 'projects_image_y')) {
                $table->decimal('projects_image_y', 5, 2)->default(50);
            }
            if (! Schema::hasColumn('site_settings', 'projects_image_zoom')) {
                $table->decimal('projects_image_zoom', 4, 2)->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['projects_image_x', 'projects_image_y', 'projects_image_zoom'] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};