<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** projects.title = the project title as text (shown on the Projects pages; the home page keeps showing the title logo) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'title')) {
                $table->string('title', 120)->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};