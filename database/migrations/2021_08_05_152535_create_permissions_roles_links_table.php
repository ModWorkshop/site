<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsRolesLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Links a user to a role instead of using a hardcoded commaified list
         * Most designs don't include an allow column. I decided we should have one.
         * Allow column essentially makes it so we can make semi-banned roles, for example ones that disallow changing your avatar.
         * Otherwise we'd have to make an edge case for each, this also makes making a banned role but without hardcoding it.
         * Of course, this doesn't handle showing the user that they are banned, 
         * the banning system will only partly use the roles system to achieve what it needs.
         */
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->bigInteger('permission_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->index('permission_id');
            $table->index('role_id');
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
        Schema::dropIfExists('permission_role');
    }
}
