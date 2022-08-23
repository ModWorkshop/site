<?php

use App\Models\Forum;
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
        Schema::create('forums', function (Blueprint $table) {
            $table->id();

            $table->tinyText('name');
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->timestamps();
        });

        //Global forum
        Forum::create(['name' => 'global']);

        $games = Game::all();

        foreach ($games as $game) {
            $game->forum()->create(['name' => $game->name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forums');
    }
};
