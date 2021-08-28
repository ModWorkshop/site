<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateModsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO: move to regular migration, this is only making sure I know how to update tables in the future
        Schema::table('mods', function (Blueprint $table) {
            $table->index([
                'category_id',
                'game_id',
                'submitter_uid',
                'name',
                'bump_date', 
                'publish_date',
            ]);
            $table->index('score');
            $table->index('views');
            $table->index('downloads');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mods', function (Blueprint $table) {
            $table->dropColumn('likes');
            $table->dropIndex([
                'category_id',
                'game_id',
                'submitter_uid',
                'name',
                'bump_date', 
                'publish_date',
            ]);
            $table->dropIndex(['score']);
            $table->dropIndex(['views']);
            $table->dropIndex(['downloads']);
            $table->dropIndex(['updated_at']);
        });
    }
}
