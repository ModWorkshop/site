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
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->enum('keep_owner_level', [
                'maintainer',
                'collaborator',
                'viewer',
                'contributor'
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->enum('keep_owner_level', [
                'maintainer',
                'collaborator',
                'viewer',
                'contributor'
            ])->change();

        });
    }
};
