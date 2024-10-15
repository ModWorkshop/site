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
        // A download (statistic) of a downloadable (file or link)
        Schema::create('downloadable_downloads', function (Blueprint $table) {
            $table->id();
            $table->morphs('downloadable');
            $table->integer('user_id')->unsigned()->nullable();
            $table->ipAddress()->nullable();
            $table->index('user_id');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloadable_downloads');
    }
};
