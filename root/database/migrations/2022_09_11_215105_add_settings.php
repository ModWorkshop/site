<?php

use App\Models\Setting;
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
        /**
         * Individual file size
         */
        DB::table('settings')->insert([
            'name' => 'max_file_size',
            'value' => 262144000,
            'public' => true,
            'type' => 'integer',
        ]);

        /**
         * Storage size allowed for each mod
         */
        DB::table('settings')->insert([
            'name' => 'mod_storage_size',
            'value' => 262144000,
            'public' => true,
            'type' => 'integer',
        ]);

        /**
         * Individual image file size
         */
        DB::table('settings')->insert([
            'name' => 'image_max_file_size',
            'value' => 5242880,
            'public' => true,
            'type' => 'integer',
        ]);

        DB::table('settings')->insert([
            'name' => 'mod_max_image_count',
            'value' => 20,
            'public' => true,
            'type' => 'integer'
        ]);

        DB::table('settings')->insert([
            'name' => 'discord_webhook',
            'value' => '',
            'public' => false,
            'type' => 'string'
        ]);

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
