<?php

use App\Models\Mod;
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
        Schema::table('mods', function (Blueprint $table) {
            $table->boolean('has_download')->default(false);
            $table->boolean('approved')->nullable()->default(true);
            $table->dropColumn('file_status');
        });

        ini_set('memory_limit', '4G');

        foreach (Mod::all() as $mod) {
            if ($mod->files()->count() || $mod->links()->count()) {
                $mod->update(['approved' => true, 'has_download' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('has_download');
            $table->dropColumn('approved');
            $table->tinyInteger('file_status')->default(0);
        });
    }
};
