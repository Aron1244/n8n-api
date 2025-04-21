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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Relación con la tabla plans
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            // Otros campos
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method')->nullable(); // PayPal, Stripe, etc.
            $table->string('paypal_order_id')->nullable(); // Añadido para PayPal
            $table->timestamps();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
