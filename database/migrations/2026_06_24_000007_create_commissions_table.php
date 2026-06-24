<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();   // who earns
            $table->foreignId('source_user_id')->nullable()->constrained('users')->nullOnDelete(); // who paid
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('level')->default(1);   // 1, 2 or 3 (multi-level)
            $table->unsignedInteger('amount_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('rate', 5, 2)->default(0);          // percentage applied
            $table->string('status')->default('pending');       // pending | approved | paid | rejected
            $table->foreignId('payout_id')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
