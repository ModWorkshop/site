<?php

use App\Models\Mod;
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
            $table->boolean('files_are_versions')->default(true);

            // True  - Primary download (if not set) is selected to be the top file OR top link if no files exist.
            //       - Mod version is chosen to be the primary file's version, falls back to the mod's one if not set.
            // False - No primary download unless chosen. Mod version is only set via mod.
            // NOTE: API still chooses the latest
        });

        $specialMods = Mod::where('has_download', true)
            ->whereDoesntHave('downloadRelation')
            ->whereHas('files', count: 2)
            ->get();

        print(sprintf("Detected %d mods with more than a single file (without primary download set)\n", count($specialMods)));

        foreach ($specialMods as $mod) {
            $mod->files_are_versions = false;
            $mod->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('files_are_versions');
        });
    }
};
