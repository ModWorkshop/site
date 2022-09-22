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
        Schema::table('user_cases', function (Blueprint $table) {
            $table->string('pardon_reason')->nullable();
            $table->boolean('pardoned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_cases', function (Blueprint $table) {
            $table->dropColumn('pardon_reason');
            $table->dropColumn('pardoned');
        });
    }
};
