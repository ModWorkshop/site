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
        Schema::create('supporter_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // The user that initiated this request
            $table->foreignId('supporter_package_id')->constrained()->nullOnDelete();
            $table->foreignId('supporter_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['complete', 'refunded', 'failed'])->default('complete');

            $table->float('price');
            $table->string('provider');
            $table->string('provider_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporter_transactions');
    }
};
