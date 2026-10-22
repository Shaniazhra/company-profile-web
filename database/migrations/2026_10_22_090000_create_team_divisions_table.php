<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Divisions of the team (for example Animation, Production), the division of each person, and who is shown on the Home and About pages. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('team_divisions')) {
            Schema::create('team_divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        Schema::table('crew_members', function (Blueprint $table) {
            if (! Schema::hasColumn('crew_members', 'team_division_id')) {
                $table->foreignId('team_division_id')->nullable()->constrained('team_divisions')->nullOnDelete();
            }
            if (! Schema::hasColumn('crew_members', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('crew_members', function (Blueprint $table) {
            if (Schema::hasColumn('crew_members', 'team_division_id')) {
                $table->dropConstrainedForeignId('team_division_id');
            }
            if (Schema::hasColumn('crew_members', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });

        Schema::dropIfExists('team_divisions');
    }
};