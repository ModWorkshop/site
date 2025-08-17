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
            $table->tinyInteger('parser_version')->default(1);
        });

        Schema::table('threads', function (Blueprint $table) {
            $table->tinyInteger('parser_version')->default(1);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->tinyInteger('parser_version')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('parser_version');
        });

        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('parser_version');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('parser_version');
        });
    }
};
