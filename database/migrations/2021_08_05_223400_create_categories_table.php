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
            $table->tinyText('name'); //TODO: should this be index?
            $table->tinyText('short_name')->unique()->nullable();
            $table->tinyText('desc')->default(''); // Was description
            $table->boolean('hidden')->default(false);
            $table->boolean('grid')->default(false);
            $table->tinyInteger('disporder')->unsigned()->default(0);
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('categories'); // TODO: should categories be cleaned up if their parent is erased?
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('sections');

            $table->tinyText('thumbnail')->default(''); // Was background
            $table->tinyText('banner')->default(''); //TODO: Remove
            $table->tinyText('buttons')->default('');
            $table->tinyText('webhook_url')->default('');
            $table->boolean('approval_only')->default(false);
            $table->timestamp('last_date');

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
