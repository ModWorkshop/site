<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->tinyText('desc')->default(''); // Was description
            $table->tinyInteger('disporder')->unsigned()->default(0);
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('games');

            $table->tinyText('thumbnail')->default(''); // Was background
            $table->tinyText('webhook_url')->default('');
            $table->boolean('approval_only')->default(false);
            $table->timestamp('last_date');

            $table->index('parent_id');
            $table->index('game_id');

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
        Schema::dropIfExists('categories');
    }
}
