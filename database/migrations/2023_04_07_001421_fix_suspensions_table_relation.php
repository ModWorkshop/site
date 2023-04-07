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
        Schema::table('suspensions', function (Blueprint $table) {
            $table->dropForeign('suspensions_mod_user_id_foreign');
            $table->foreign('mod_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suspensions', function (Blueprint $table) {
            $table->dropForeign('suspensions_mod_user_id_foreign');
        });
    }
};
