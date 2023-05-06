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
        Schema::table('mods', function (Blueprint $table) {
            $table->dropForeign('mods_thumbnail_id_foreign');
            $table->dropForeign('mods_banner_id_foreign');
            $table->foreign('thumbnail_id')->references('id')->on('images')->nullOnDelete();
            $table->foreign('banner_id')->references('id')->on('images')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            //
        });
    }
};
