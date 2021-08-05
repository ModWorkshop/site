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
            $table->tinyText('short_name')->unique()->default('');
            $table->tinyText('desc')->unique()->default(''); // Was description
            $table->boolean('hidden');
            $table->boolean('grid');
            $table->tinyInteger('disporder')->unsigned();
            $table->bigInteger('parent')->unsigned();
            $table->foreign('parent')->references('id')->on('categories'); // TODO: should categories be cleaned up if their parent is erased?
            //Check whether we can set it to 0 to have it as a game category.
            //Worst case I imagine we can set it to null.
            $table->bigInteger('root')->unsigned();
            $table->foreign('root')->references('id')->on('categories');

            $table->tinyText('thumbnail')->default(''); // Was background
            $table->tinyText('banner')->default(''); // Was background
            $table->tinyText('buttons')->default(''); // Was background
            $table->tinyText('webhook_url')->default(''); // Was background
            $table->boolean('approval_only');
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
