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
        Schema::create('supporter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // The user that initiated this request
            $table->foreignId('supporter_package_id')->constrained()->nullOnDelete();
            $table->enum('status', ['waiting', 'active', 'cancelled'])->default('waiting');

            $table->float('price');
            $table->string('provider');
            $table->string('provider_id'); // Tebex's case it's the reccuring payment ID

            $table->timestamp('next_payment_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporter_subscriptions');
    }
};
