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
        Schema::create('user_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_bot_id')->constrained()->onDelete('cascade');
            $table->string('license_key')->unique();
            $table->enum('status', ['active', 'inactive', 'expired', 'suspended']);
            $table->timestamp('activated_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('bot_config')->nullable(); // User's custom bot configuration
            $table->string('exchange_connected')->nullable(); // Which exchange user connected
            $table->json('performance_stats')->nullable(); // Bot performance data
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('license_key');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bots');
    }
};
