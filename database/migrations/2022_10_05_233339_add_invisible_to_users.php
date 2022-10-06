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
        Schema::table('users', function (Blueprint $table) {
            $table->string('banner')->default('');
            $table->text('bio')->default('');
            $table->boolean('private_profile')->default(false);
            $table->tinyText('custom_title')->default('');
            $table->boolean('invisible')->default(false);
            $table->tinyText('donation_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropcolumn('invisible');
            $table->dropcolumn('banner');
            $table->dropcolumn('bio');
            $table->dropcolumn('private_profile');
            $table->dropcolumn('custom_title');
            $table->dropcolumn('donation_url');
        });
    }
};
