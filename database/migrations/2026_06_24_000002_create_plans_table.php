<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();              // free | premium | pro
            $table->string('name');
            $table->unsignedTinyInteger('level');          // 1 = free, 2 = premium, 3 = pro
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->string('interval')->default('month');  // month | year | lifetime
            $table->unsignedInteger('minutes_limit')->nullable();   // null = unlimited monthly minutes
            $table->string('engine')->default('browser');  // browser | cloud — translation engine allowed
            $table->boolean('allow_system_audio')->default(false);  // capture tab/system audio
            $table->boolean('ads')->default(true);
            $table->json('features')->nullable();          // marketing bullet points (per locale or keys)
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
