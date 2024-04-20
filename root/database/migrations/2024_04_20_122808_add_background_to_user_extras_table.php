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
            $table->string('background')->default('');
            $table->float('background_opacity', unsigned: true)->default(0.1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_extras', function (Blueprint $table) {
            $table->dropColumn('background');
            $table->dropColumn('background_opacity');
        });
    }
};
