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
        Schema::create('mod_views', function (Blueprint $table) {
            $table->id();
            $table->integer('mod_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->ipAddress()->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('mod_id');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mod_views');
    }
};
