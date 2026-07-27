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
        Schema::table('taggables', function (Blueprint $table) {
            $table->boolean('applied_by_mod')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropColumn('applied_by_mod');
        });
    }
};
