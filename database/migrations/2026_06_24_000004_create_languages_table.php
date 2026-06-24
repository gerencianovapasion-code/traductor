<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();      // BCP-47 base, e.g. es, en, pt-BR, zh
            $table->string('name');                    // English name
            $table->string('native_name')->nullable(); // endonym
            $table->string('flag', 8)->nullable();     // emoji flag
            $table->string('speech_code', 16)->nullable(); // Web Speech locale, e.g. es-ES
            $table->boolean('can_listen')->default(true);   // STT / source detection available
            $table->boolean('can_speak')->default(true);    // TTS available
            $table->boolean('ui')->default(false);     // available as UI interface language
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
