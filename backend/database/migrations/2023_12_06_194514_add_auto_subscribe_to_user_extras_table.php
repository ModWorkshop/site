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
        Schema::table('user_extras', function (Blueprint $table) {
            $table->boolean('auto_subscribe_to_mod')->default(true);
            $table->boolean('auto_subscribe_to_thread')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_extras', function (Blueprint $table) {
            $table->dropColumn('auto_subscribe_to_mod');
            $table->dropColumn('auto_subscribe_to_thread');
        });
    }
};
