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
        Schema::create('user_cases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();

            $table->bigInteger('mod_user_id')->unsigned()->nullable();
            $table->foreign('mod_user_id')->references('id')->on('users')->nullOnDelete();

            $table->text('reason');

            $table->timestamp('expire_date')->nullable();

            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->boolean('active')->default(true);

            $table->index('user_id');
            $table->index('mod_user_id');
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
        Schema::dropIfExists('user_cases');
    }
};
