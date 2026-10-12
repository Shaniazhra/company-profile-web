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
    Schema::create('site_settings', function (Blueprint $table) {
        $table->id();
        $table->string('site_name')->default('Dream Ratio');
        $table->string('logo')->nullable();
        $table->string('favicon')->nullable();
        $table->json('tagline')->nullable();
        $table->json('heart_statement')->nullable();
        $table->json('impact_statement')->nullable();
        $table->json('about_text')->nullable();
        $table->string('email')->nullable();
        $table->string('instagram_url')->nullable();
        $table->string('linkedin_url')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
