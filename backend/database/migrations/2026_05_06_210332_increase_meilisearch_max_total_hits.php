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
        $client = new \Meilisearch\Client('http://meilisearch:7700', env('MEILISEARCH_KEY'));
        $index = $client->index('mods');

        // If anyone knows a better way I'm all ears, but from quick testing there's no effect and we DO want to get a correct total results
        $index->updatePagination(['maxTotalHits' => 50000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $client = new \Meilisearch\Client('http://meilisearch:7700', env('MEILISEARCH_KEY'));
        $index = $client->index('mods');

        $index->updatePagination(['maxTotalHits' => 1000]);
    }
};
