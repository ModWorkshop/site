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
        Schema::table('mods', function (Blueprint $table) {
            $table->bigInteger('background_id')->unsigned()->nullable();
            $table->foreign('background_id')->references('id')->on('images')->nullOnDelete();
            $table->float('background_opacity')->default(0.1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('background_id');
            $table->dropColumn('background_opacity');
        });
    }
};
