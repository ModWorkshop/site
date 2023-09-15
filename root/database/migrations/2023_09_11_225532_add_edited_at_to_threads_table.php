<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Thread;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable();
        });

        $threads = Thread::get();

        foreach ($threads as $thread) {
            Thread::withoutTimestamps(
                fn() => $thread->update([
                    'edited_at' => $thread->updated_at
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });
    }
};
