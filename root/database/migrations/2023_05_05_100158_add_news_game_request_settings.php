<?php

use App\Models\Setting;
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
        Setting::forceCreate([
            'name' => 'news_forum_category',
            'value' => '',
            'public' => true,
            'type' => 'integer'
        ]);

        Setting::forceCreate([
            'name' => 'game_requests_forum_category',
            'value' => '',
            'public' => true,
            'type' => 'integer'
        ]);

        Setting::flushQueryCache();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('name', 'game_requests_forum_category')->delete();
        Setting::where('name', 'news_forum_category')->delete();

        Setting::flushQueryCache();
    }
};
