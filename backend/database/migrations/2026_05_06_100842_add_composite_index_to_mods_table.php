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
            $table->rawIndex(
                'visibility, published_at, suspended, approved, has_download, game_id',
                'mods_composite_index_vis_publishedat_sus_app_hasdownload_game'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropIndex('mods_composite_index_vis_publishedat_sus_app_hasdownload_game');
        });
    }
};
