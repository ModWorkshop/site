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
        /**
         * This used to be stored in a JSON string and that was a lazy and terrible solution
         * Obviously I had to essentially make sure the data is valid so things don't break
         * No more. This is now a proper table.
         * You'll notice that we have *both* mod relation and url/name. While I could make another table just for the separation,
         * this will be a bit annoying to sort.
         */
        
        Schema::create('dependencies', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name')->nullable();
            $table->text('url')->nullable();
            $table->boolean('offsite');
            $table->bigInteger('mod_id')->unsigned()->nullable();
            $table->boolean('optional')->default(true);
            $table->morphs('dependable'); //Mod or template
            $table->smallInteger('order')->default(1);
            $table->index('order');
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
        Schema::dropIfExists('dependencies');
    }
};
