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
            $table->dropColumn('default_mods_sort');
            $table->tinyText('home_default_mods_sort')->nullable();
            $table->tinyText('game_default_mods_sort')->nullable();
            $table->tinyText('default_mods_sort')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_extras', function (Blueprint $table) {
            $table->dropColumn('home_default_mods_sort');
            $table->dropColumn('game_default_mods_sort');
        });
    }
};
