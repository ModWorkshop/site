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
        Schema::create('pending_files', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name');
            $table->tinyText('file_name');
            $table->tinyText('file_type');
            $table->bigInteger('size')->default(0);
            $table->boolean('completed')->default(false);

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('mod_id')->unsigned();
            $table->foreign('mod_id')->references('id')->on('mods')->onDelete('cascade');
            $table->bigInteger('file_id')->unsigned()->nullable();
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_files');
    }
};
