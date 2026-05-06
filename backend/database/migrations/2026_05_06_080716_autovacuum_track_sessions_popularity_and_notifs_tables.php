<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE track_sessions SET (autovacuum_vacuum_scale_factor = 0.05);');
        DB::statement('ALTER TABLE popularity_logs SET (autovacuum_vacuum_scale_factor = 0.05);');
        DB::statement('ALTER TABLE notifications SET (autovacuum_vacuum_scale_factor = 0.1);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
