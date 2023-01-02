<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->char('color', 8);
            $table->string('notice')->default('');
            $table->tinyText('notice_type')->nullable();
            $table->boolean('notice_localized')->default(true);
            $table->bigInteger('game_id')->nullable()->unsigned();
            $table->foreign('game_id')->references('id')->on('games');
            $table->string('type')->default('');
            
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
        Schema::dropIfExists('tags');
    }
}
