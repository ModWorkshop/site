<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('popularity_logs', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        Schema::table('mod_views', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        Schema::table('mod_downloads', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popularity_logs', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('mod_views', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('mod_downloads', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
