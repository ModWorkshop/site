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
        DB::raw('CREATE EXTENSION IF NOT EXISTS "semver"');
        Schema::table('files', function (Blueprint $table) {
            DB::statement('ALTER TABLE files ADD semver_version SEMVER');
            DB::statement('CREATE INDEX files_semver_version_index ON files(semver_version)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('semver_version');
            DB::statement('DROP INDEX IF EXISTS files_semver_version_index;');
        });

    }
};
