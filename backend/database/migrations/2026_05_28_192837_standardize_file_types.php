<?php

use App\Models\File;
use App\Services\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->tinyText('legacy_mime')->nullable();
        });

        File::where('type', 'ILIKE', '%/%')
            ->chunkById(1000, function(Collection $files) {
                foreach ($files as $file) {
                    $file->timestamps = false;
                    $file->updateQuietly([
                        'type' => Utils::safeFileType($file->file, 1),
                        'legacy_mime' => $file->getRawOriginal('type')
                    ]);
                }
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('legacy_mime');
        });

    }
};
