<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mod;
use App\Models\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \set_time_limit(600);
        ini_set('memory_limit', '1G');
        Mod::setEagerLoads([])->whereNotNull('download_id')->where('download_type', 'file')->chunkById(1000, function($mods) {
            foreach ($mods as $mod) {
                if (!File::where('id', $mod->download_id)->exists()) {
                    $mod->download_id = null;
                    $mod->download_type = null;
                    $mod->save();
                }                
            }
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
