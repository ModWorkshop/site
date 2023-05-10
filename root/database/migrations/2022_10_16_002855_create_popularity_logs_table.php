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
        /**
         * This table is gonna be huge, but we'll optimize it in the following ways: 
         * 1. Delete old entries (1 year?)
         * 2. Use smaller datatypes
         */
        Schema::create('popularity_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['view', 'down', 'like']);

            $table->integer('mod_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->ipAddress()->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->index('ip_address');
            $table->index('type');
            $table->index('updated_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('popularity_logs');
    }
};
