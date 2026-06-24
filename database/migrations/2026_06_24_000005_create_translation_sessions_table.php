<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_lang', 12)->nullable();
            $table->string('target_lang', 12);
            $table->string('engine')->default('browser'); // browser | cloud
            $table->unsignedInteger('seconds')->default(0);
            $table->unsignedInteger('characters')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_sessions');
    }
};
