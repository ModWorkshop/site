<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('CREATE INDEX CONCURRENTLY users_name_trigram ON users USING GIST (name gist_trgm_ops(siglen=64));');
            DB::statement('CREATE INDEX CONCURRENTLY users_unique_name_trigram ON users USING GIST (unique_name gist_trgm_ops(siglen=64));');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
