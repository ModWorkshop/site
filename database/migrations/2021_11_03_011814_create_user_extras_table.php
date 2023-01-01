<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extras', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyText('default_mods_view')->default('all');
            $table->tinyText('default_mods_sort')->default('bumped_at');
            $table->boolean('home_show_last_games')->default(true);
            $table->boolean('home_show_mods')->default(true);
            $table->boolean('home_show_threads')->default(true);
            $table->boolean('game_show_mods')->default(true);
            $table->boolean('game_show_threads')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_extras');
    }
}
