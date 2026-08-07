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
        Schema::table('pending_files', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mod_id']);
            $table->dropForeign(['file_id']);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('mod_id')->references('id')->on('mods')->nullOnDelete();
            $table->foreign('file_id')->references('id')->on('files')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
