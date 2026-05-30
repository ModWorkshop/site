<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('needs_mod_approval')->default(false);
        });

        // Set this to true to users registered in the last month with no mods
        User::where('created_at', '>', Carbon::now()->subMonths(1))->chunkById(1000, function(Collection $users) {
            foreach ($users as $user) {
                $approvedMods = $user->mods()->where('approved', true)->count();
                if ($approvedMods == 0) {
                    $user->timestamps = false;
                    $user->update([
                        'needs_mod_approval' => true
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('needs_mod_approval');
        });
    }
};
