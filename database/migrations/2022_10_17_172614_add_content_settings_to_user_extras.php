<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_extras', function (Blueprint $table) {
            $table->tinyText('default_mods_view')->default('all');
            $table->tinyText('default_mods_sort')->default('bumped_at');
            $table->boolean('home_show_last_games')->default(true);
            $table->boolean('home_show_mods')->default(true);
            $table->boolean('home_show_threads')->default(true);
            $table->boolean('game_show_mods')->default(true);
            $table->boolean('game_show_threads')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_extras', function (Blueprint $table) {
            $table->dropColumn('default_mods_view');
            $table->dropColumn('default_mods_sort');
            $table->dropColumn('home_show_last_games');
            $table->dropColumn('home_show_mods');
            $table->dropColumn('home_show_threads');
            $table->dropColumn('game_show_mods');
            $table->dropColumn('game_show_threads');
        });
    }
};
