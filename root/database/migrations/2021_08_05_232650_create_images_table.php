<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('mod_id')->unsigned();
            $table->foreign('mod_id')->references('id')->on('mods')->onDelete('cascade');
            $table->boolean('has_thumb')->default(false);
            $table->tinyText('file');
            $table->tinyText('type');
            $table->bigInteger('size');
            $table->timestamps();

            $table->index('user_id');
            $table->index('mod_id');
        });

        Schema::table('mods', function (Blueprint $table) {
            $table->bigInteger('thumbnail_id')->unsigned()->nullable();
            $table->foreign('thumbnail_id')->references('id')->on('images')->nullOnDelete();
            $table->bigInteger('banner_id')->unsigned()->nullable();
            $table->foreign('banner_id')->references('id')->on('images')->nullOnDelete();
        })
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
