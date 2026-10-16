<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->string('question');
            $t->text('answer');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('process_stages', function (Blueprint $t) {
            $t->id();
            $t->string('title', 80);
            $t->string('summary')->nullable();
            $t->text('details')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('crew_members', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('role', 120);
            $t->string('photo')->nullable();
            $t->text('credits')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('contact_people', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('role', 120)->nullable();
            $t->string('phone', 40)->nullable();
            $t->string('email', 190)->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('contact', 190);
            $t->text('message');
            $t->boolean('is_read')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['inquiries', 'contact_people', 'crew_members', 'process_stages', 'faqs'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};