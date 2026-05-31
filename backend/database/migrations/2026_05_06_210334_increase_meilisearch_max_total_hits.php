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
        $client = new \Meilisearch\Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $mods = $client->index('mods');
        $threads = $client->index('threads');
        $users = $client->index('users');

        // If anyone knows a better way I'm all ears, but from quick testing there's no effect and we DO want to get a correct total results
        $mods->updatePagination(['maxTotalHits' => 50000]);
        $threads->updatePagination(['maxTotalHits' => 5000]);
        $users->updatePagination(['maxTotalHits' => 5000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $client = new \Meilisearch\Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $mods = $client->index('mods');
        $threads = $client->index('threads');
        $users = $client->index('users');

        // If anyone knows a better way I'm all ears, but from quick testing there's no effect and we DO want to get a correct total results
        $mods->updatePagination(['maxTotalHits' => 1000]);
        $threads->updatePagination(['maxTotalHits' => 1000]);
        $users->updatePagination(['maxTotalHits' => 1000]);
    }
};
