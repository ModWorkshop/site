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
        Schema::table('popularity_logs', function (Blueprint $table) {
            $table->index('user_id');
            // $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popularity_logs', function (Blueprint $table) {
            $table->dropIndex('user_id');
            // $table->dropIndex('ip_address');
        });
    }
};
