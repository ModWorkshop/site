<?php

use App\Models\Category;
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
        $categories = Category::all();

        $highestId = 1;

        foreach ($categories as $category) {
            if ($category->parent == null) {
                $game = new Game();

                $game->id = $category->id;
                $game->name = $category->name;
                $game->short_name = $category->short_name;
                $game->thumbnail = $category->thumbnail;
                $game->banner = $category->banner;
                $game->buttons = $category->buttons;
                $game->webhook_url = $category->webhook_url;
                $game->last_date = $category->last_date;

                if ($category->id > $highestId) {
                    $highestId = $category->id;
                }

                $game->save();
            }
        }
        
        $highestId++;

        DB::statement("ALTER SEQUENCE categories_id_seq RESTART $highestId;");

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_game_id_foreign');
            $table->foreign('game_id')->references('id')->on('sections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('game_id');
            $table->foreign('game_id')->references('id')->on('categories');
        });

        DB::table('sections')->truncate();
        DB::statement("ALTER SEQUENCE categories_id_seq RESTART 1;");
    }
};
