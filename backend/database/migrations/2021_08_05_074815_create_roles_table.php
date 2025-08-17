<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->tinyText('tag')->default(''); // A tag next to the name like Moderator
            $table->text('desc')->default('');
            $table->tinyText('color')->default(''); // Hex, unset by default.
            /**
             * Order works like this, the lower the number is, the higher it is. This just avoids having to update the number each time we add a role.
             * So let's say Admin is 1, we add a new role, the role gets its order assigned as 2, so by default newer roles are weaker.
             * When changing orders we'll need to reorder all roles.
             * This means that when ordering, we'll use desecending order.
             * Also we must avoid roles having the same order.
             */
            $table->smallInteger('order');
            $table->boolean('is_vanity')->default(false);

            $table->boolean('self_assignable')->default(false);

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
        Schema::dropIfExists('roles');
    }
}
