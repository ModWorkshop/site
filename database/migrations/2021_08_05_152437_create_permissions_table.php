<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            // Unlike roles, permissions have a unique 'name'.
            // For example: 'edit mod' permission then using the can:edit mod check in routes.
            $table->tinyText('name')->unique();
            $table->timestamps();
            $table->enum('type', ['global', 'game'])->nullable()->default('global');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
