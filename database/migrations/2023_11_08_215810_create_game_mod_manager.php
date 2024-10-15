<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_mod_manager', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('mod_manager_id')->unsigned();
            $table->foreign('mod_manager_id')->references('id')->on('mod_managers')->onDelete('cascade');

            $table->bigInteger('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_mod_manager');
    }
};
