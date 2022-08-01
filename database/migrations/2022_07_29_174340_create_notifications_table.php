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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); //Any model that can be notified for. For example: Mod, Comment, Thread.
            $table->morphs('context'); //Context for the notification. "You got a reply in a comment (notifiable) from user (context).
            $table->tinyText('type'); //Type of notification, reply_comment.
            $table->boolean('seen')->default(false);
            $table->json('data')->nullable(); //Extra context if necessary, hopefully not.
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
        Schema::dropIfExists('notifications');
    }
};
