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
        DB::select("SELECT SETVAL(pg_get_serial_sequence('forum_categories', 'id'), (SELECT MAX(id) FROM forum_categories));");
        DB::select("SELECT SETVAL(pg_get_serial_sequence('threads', 'id'), (SELECT MAX(id) FROM threads));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
