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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nowpayments_payment_id')->unique();
            $table->string('nowpayments_order_id')->nullable();
            $table->enum('payment_status', ['waiting', 'confirming', 'confirmed', 'sending', 'partially_paid', 'finished', 'failed', 'refunded', 'expired']);
            $table->decimal('pay_amount', 15, 8);
            $table->string('pay_currency', 10);
            $table->decimal('price_amount', 10, 2);
            $table->string('price_currency', 3);
            $table->string('pay_address')->nullable();
            $table->decimal('actually_paid', 15, 8)->nullable();
            $table->string('outcome_amount')->nullable();
            $table->string('outcome_currency')->nullable();
            $table->json('payment_extra')->nullable(); // Additional payment data from NowPayments
            $table->text('description')->nullable();
            $table->timestamp('payment_created_at')->nullable();
            $table->timestamp('payment_updated_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'payment_status']);
            $table->index('nowpayments_payment_id');
            $table->index('payment_status');
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
