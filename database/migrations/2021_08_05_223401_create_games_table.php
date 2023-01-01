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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->tinyText('short_name')->unique();
            $table->tinyInteger('disporder')->unsigned()->default(0);

            $table->tinyText('thumbnail')->default(''); // Was background
            $table->tinyText('banner')->default(''); // Was background
            $table->tinyText('buttons')->default(''); // Was background
            $table->tinyText('webhook_url')->default(''); // Was background
            $table->timestamp('last_date');

            $table->bigInteger('forum_id')->nullable()->unsigned();
            $table->foreign('forum_id')->references('id')->on('forums');

            $table->bigInteger('mods_count')->default(0);

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
        Schema::dropIfExists('games');
    }
};
