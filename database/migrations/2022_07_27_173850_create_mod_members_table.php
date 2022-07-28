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
        Schema::create('mod_members', function (Blueprint $table) {
            $table->id();
            // Replaces mod's collaborators and invited only
            //Level 1 - Maintainer
            //Level 2 - Collaborator
            //Level 3 - Viewer
            //Level 4 - Contributor 
            $table->tinyInteger('level');
            $table->boolean('accepted'); //If the member doesn't accept by around a day (or more), the member gets deleted.
            $table->bigInteger('mod_id')->unsigned();
            $table->foreign('mod_id')->references('id')->on('mods')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('mod_members');
    }
};
