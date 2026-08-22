<?php

use App\Models\Game;
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
        foreach (Game::all() as $game) {
            $forumCat = $game->forum->categories()->firstOrCreate([
                'name' => 'Tickets',
            ],[
                'display_order' => 60,
                'emoji' => '🎟️',
                'desc' => 'Forum category for getting help from moderators or appealing moderation decisions.',
                'can_close_threads' => true,
                'private_threads' => true,
                'tickets_mode' => true,
            ]);

            $game->update([
                'appeals_forum_category_id' => $forumCat->id
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
