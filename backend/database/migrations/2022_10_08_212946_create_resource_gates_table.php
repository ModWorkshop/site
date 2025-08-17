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
        Schema::create('forum_category_roles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('forum_category_id');
            $table->morphs('role');
            $table->boolean('can_view');
            $table->boolean('can_post');
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
        Schema::dropIfExists('forum_category_roles');
    }
};
