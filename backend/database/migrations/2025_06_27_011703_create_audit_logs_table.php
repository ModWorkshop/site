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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // CRUD actions, ban, unban, warn, suspend mod, unsuspend mod
            $table->tinyText('type');

            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->foreignId('game_id')->nullable()->constrained()->nullOnDelete();

            $table->nullableMorphs('auditable');
            $table->nullableMorphs('context');
            $table->json('data')->nullable();

            // Name in case the auditable or context object are deleted
            $table->tinyText('auditable_name')->nullable();
            $table->tinyText('context_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
