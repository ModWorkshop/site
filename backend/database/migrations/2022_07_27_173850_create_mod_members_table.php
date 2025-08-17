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
            $table->enum('level', [
                'maintainer',
                'collaborator',
                'viewer',
                'contributor'
            ]);
            $table->boolean('accepted'); //If the member doesn't accept by around a day (or more), the member gets deleted.
            $table->bigInteger('mod_id')->unsigned();
            $table->foreign('mod_id')->references('id')->on('mods')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('user_id');
            $table->index('mod_id');
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
