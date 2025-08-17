<?php

use App\Models\Forum;
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
        foreach (Forum::whereNotNull('game_id')->get() as $forum) {
            $forum->createDefaultCategories();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
