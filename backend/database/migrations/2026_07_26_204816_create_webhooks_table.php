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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();

            $table->tinyText('name');

            $table->boolean('event_mod_approval')->default(false);
            $table->boolean('event_mod_approval_new')->default(false);;
            $table->boolean('event_mod_deleted')->default(false);;
            $table->boolean('event_mod_suspended')->default(false);;
            $table->boolean('event_mod_published')->default(false);;
            $table->boolean('event_mod_bumped')->default(false);;
            $table->boolean('event_file_uploaded')->default(false);;
            $table->boolean('event_report_new')->default(false);;

            $table->boolean('is_active');

            $table->text('url');

            $table->text('custom_template')->default('');

            $table->foreignId('game_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
