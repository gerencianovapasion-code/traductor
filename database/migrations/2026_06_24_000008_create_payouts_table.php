<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->string('method')->default('paypal');   // paypal | bank | balance
            $table->string('destination')->nullable();      // paypal email / IBAN
            $table->string('status')->default('requested'); // requested | approved | paid | rejected
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
