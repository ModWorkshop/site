<?php

use App\Models\Game;
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
        Schema::table('games', function (Blueprint $table) {
            $table->bigInteger('forum_id')->nullable()->unsigned();
            $table->foreign('forum_id')->references('id')->on('forums');
        });

        $games = Game::all();
        foreach ($games as $game) {
            $game->forum_id = $game->forum->id;
            $game->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('forum_id');
        });
    }
};
