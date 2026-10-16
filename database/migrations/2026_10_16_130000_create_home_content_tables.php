<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highlights', function (Blueprint $t) {
            $t->id();
            $t->string('group', 20)->index();
            $t->string('title', 120)->nullable();
            $t->string('text', 400);
            $t->string('icon', 30)->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('focus_shots', function (Blueprint $t) {
            $t->id();
            $t->string('title', 120);
            $t->string('image')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_shots');
        Schema::dropIfExists('highlights');
    }
};