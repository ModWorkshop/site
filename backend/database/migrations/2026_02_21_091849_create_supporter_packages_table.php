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
        Schema::create('supporter_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('enabled');
            $table->integer('order');
            $table->bigInteger('package_id');
            $table->integer('price');
            $table->integer('duration_number');
            $table->enum('duration_type', ['mo', 'y', 'w', 'd']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporter_packages');
    }
};
