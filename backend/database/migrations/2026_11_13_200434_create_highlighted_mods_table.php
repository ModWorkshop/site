@@ -0,0 +1,30 @@
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
        Schema::create('highlighted_mods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mod_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('status')->default(true);
            $table->index('game_id');
            $table->timestamps();
        });

        Schema::table('mods', function(Blueprint $table) {
            $table->boolean('highlighted')->default(false);
            $table->index('highlighted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highlighted_mods');
    }
};
