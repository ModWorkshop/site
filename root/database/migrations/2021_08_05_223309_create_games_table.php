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

            $table->tinyText('thumbnail')->default(''); // Was background
            $table->tinyText('banner')->default(''); // Was background
            $table->tinyText('buttons')->default(''); // Was background
            $table->tinyText('webhook_url')->default(''); // Was background
            $table->dateTime('last_date')->nullable();

            $table->timestamps();

            $table->index('last_date');
            $table->index('short_name');
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
