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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->string('content');
            $table->bigInteger('views')->default(0);
            $table->bigInteger('comments_count')->default(0);
            $table->boolean('locked')->default(false);
            $table->timestamp('bumped_at')->nullable();
            $table->timestamp('pinned_at')->nullable();
            $table->bigInteger('forum_id')->unsigned();
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');

            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('forum_categories')->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('last_user_id')->unsigned();
            $table->foreign('last_user_id')->references('id')->on('users');

            $table->boolean('locked_by_mod')->default(false);

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
        Schema::dropIfExists('threads');
    }
};
