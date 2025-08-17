<?php

use App\Models\UserExtra;
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
        $extras = UserExtra::where('default_mods_view', 'games')
            ->orWhere('default_mods_view', 'users')
            ->orWhere('default_mods_view', 'mods')
            ->orWhere('default_mods_view', 'liked')
            ->get();

        foreach($extras as $extra) {
            $extra->update([
                'default_mods_view' => 'followed'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
