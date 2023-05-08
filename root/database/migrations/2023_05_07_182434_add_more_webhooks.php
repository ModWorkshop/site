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
            'name' => 'discord_approval_webhook',
            'value' => '',
            'public' => false,
            'type' => 'string'
        ]);

        Setting::forceCreate([
            'name' => 'discord_suspension_webhook',
            'value' => '',
            'public' => false,
            'type' => 'string'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
