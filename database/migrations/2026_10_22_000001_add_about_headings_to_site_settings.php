<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['about_label', 'about_title', 'story_title', 'cta_label'];

    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $t) {
            foreach ($this->columns as $c) {
                if (! Schema::hasColumn('site_settings', $c)) {
                    $t->text($c)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        // Kolom sengaja tidak dihapus supaya isi yang sudah diketik admin tidak hilang.
    }
};