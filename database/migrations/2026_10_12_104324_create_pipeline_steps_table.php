<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('pipeline_steps', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // traditional | dream_ratio
        $table->json('title');
        $table->json('sub_points')->nullable();
        $table->unsignedInteger('sort_order')->default(0);
        $table->boolean('is_published')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_steps');
    }
};
