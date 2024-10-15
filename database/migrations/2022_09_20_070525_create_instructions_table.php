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
        Schema::create('instructs_templates', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->text('instructions');
            $table->boolean('localized')->default(false);
            //Was categories, we only support one game now for consistency
            $table->bigInteger('game_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->index('game_id');

            $table->timestamps();
        });

        Schema::table('mods', function (Blueprint $table) {
            $table->bigInteger('instructs_template_id')->unsigned()->nullable();
            $table->foreign('instructs_template_id')->references('id')->on('instructs_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('instructs_template_id');
        });
        Schema::dropIfExists('instructs_templates');
    }
};
