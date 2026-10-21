<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('title')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Isi judul kosong dulu supaya kolom bisa kembali NOT NULL.
        DB::table('projects')->whereNull('title')->update(['title' => '{}']);

        Schema::table('projects', function (Blueprint $table) {
            $table->json('title')->nullable(false)->change();
        });
    }
};