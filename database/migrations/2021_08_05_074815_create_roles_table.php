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
        /**
         * Previously we'd have a primary group, the new system will not utilize such concept.
         * Similarly to Discord, users have roles and sometimes also 'muted' role, there's no such thing as a primary role.
         * 
         * Roles work by orders, the higher the role is, the more control it will have.
         * For example, if you have a moderator role, and the banned role is below it, even with the role applied, the moderator role
         * will be the deciding one.
         */
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
            $table->smallInteger('order')->unique();
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
