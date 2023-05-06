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
        Schema::table('mod_views', function (Blueprint $table) {
            $table->dropForeign('mod_views_mod_id_foreign');
            $table->dropForeign('mod_views_pkey');
            $table->dropForeign('mod_views_user_id_foreign');
            $table->dropColumn('id');
            $table->integer('mod_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->change();

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('mod_downloads', function (Blueprint $table) {
            $table->dropForeign('mod_downloads_mod_id_foreign');
            $table->dropForeign('mod_downloads_pkey');
            $table->dropForeign('mod_downloads_user_id_foreign');
            $table->dropColumn('id');
            $table->integer('mod_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->change();

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('popularity_logs', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->integer('mod_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
