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
        Setting::insert([
            'name' => 'new_user_first_upload_requires_approval',
            'value' => true,
            'public' => true,
            'type' => 'boolean',
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('name', 'new_user_first_upload_requires_approval')->delete();
    }
};
