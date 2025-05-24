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
        Schema::create('expert_signals', function (Blueprint $table) {
            $table->id();
            $table->string('pair'); // e.g., BTC/USD, EUR/USD
            $table->enum('signal_type', ['BUY', 'SELL', 'HODL']);
            $table->decimal('entry_price', 15, 8);
            $table->decimal('take_profit', 15, 8)->nullable();
            $table->decimal('stop_loss', 15, 8)->nullable();
            $table->text('analysis_reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'published']);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable(); // Additional signal data
            $table->timestamps();
            
            $table->index(['pair', 'status']);
            $table->index('published_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_signals');
    }
};
