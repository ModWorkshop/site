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
        Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->boolean('can_appeal')->default(true);
            $table->timestamps();
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->timestamp('expire_date')->nullable();
            $table->text('reason');

            $table->bigInteger('mod_user_id')->unsigned()->nullable();
            $table->foreign('mod_user_id')->references('id')->on('users')->nullOnDelete();

            $table->boolean('active')->default(true);

            $table->index('user_id');
            $table->index('game_id');
            $table->index('case_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bans');
    }
};
