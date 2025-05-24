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
        Schema::create('trading_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('version')->default('1.0.0');
            $table->enum('bot_type', ['scalping', 'swing', 'arbitrage', 'grid', 'dca']);
            $table->json('supported_exchanges'); // Array of supported exchanges
            $table->json('supported_pairs'); // Array of supported trading pairs
            $table->string('file_path'); // Path to bot file
            $table->string('file_hash'); // File integrity hash
            $table->decimal('min_balance', 10, 2)->default(0); // Minimum balance required
            $table->json('default_config'); // Default bot configuration
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_license')->default(true);
            $table->string('license_key_prefix')->nullable();
            $table->text('installation_guide')->nullable();
            $table->timestamps();
            
            $table->index(['bot_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_bots');
    }
};
