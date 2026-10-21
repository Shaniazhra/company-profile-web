<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Hero photo at the top of the public Team page, with its position (x and y in percent) and zoom (1 = no zoom) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'team_image')) {
                $table->string('team_image')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'team_image_x')) {
                $table->decimal('team_image_x', 5, 2)->default(50);
            }
            if (! Schema::hasColumn('site_settings', 'team_image_y')) {
                $table->decimal('team_image_y', 5, 2)->default(50);
            }
            if (! Schema::hasColumn('site_settings', 'team_image_zoom')) {
                $table->decimal('team_image_zoom', 4, 2)->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['team_image', 'team_image_x', 'team_image_y', 'team_image_zoom'] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};