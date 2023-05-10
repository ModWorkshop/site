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
        Schema::create('suspensions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mod_id')->unsigned();
            $table->foreign('mod_id')->references('id')->on('mods')->onDelete('cascade');
            $table->bigInteger('mod_user_id')->unsigned()->nullable();
            $table->foreign('mod_user_id')->references('id')->on('users')->nullOnDelete();
            $table->text('reason')->default('');
            $table->boolean('status')->default(true);

            $table->index('mod_user_id');
            $table->index('mod_id');

            $table->dropForeign('suspensions_mod_user_id_foreign');

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
        Schema::dropIfExists('suspensions');
    }
};
