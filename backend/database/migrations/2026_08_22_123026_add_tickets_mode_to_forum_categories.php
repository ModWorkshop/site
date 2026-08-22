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
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->boolean('tickets_mode')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('tickets_mode');
        });
    }
};
