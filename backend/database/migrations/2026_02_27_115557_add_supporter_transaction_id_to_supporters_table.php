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
        Schema::table('supporters', function (Blueprint $table) {
            $table->integer('price')->default(0);

            $table->foreignId('supporter_package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supporter_transaction_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supporters', function (Blueprint $table) {
            $table->dropColumn('supporter_transaction_id');
            $table->dropColumn('supporter_package_id');
            $table->dropColumn('price');
        });
    }
};
