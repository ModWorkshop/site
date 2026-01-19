<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        Schema::create('mods', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('games');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->tinyText('name');
            $table->text('desc'); // Was description
            $table->tinyText('short_desc')->default(''); // Was short_description
            $table->text('changelog')->default('');
            $table->text('license')->default('');
            $table->text('instructions')->default('');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->tinyText('legacy_banner_url')->nullable(); //Only to be used for old mods, all new mods will have to move to banner_id.
            $table->bigInteger('downloads')->unsigned()->default(0);
            $table->bigInteger('likes')->unsigned()->default(0);
            $table->bigInteger('views')->unsigned()->default(0);
            $table->tinyText('version')->default('');
            $table->tinyText('donation')->default(''); // Was receiver_email

            $table->boolean('suspended')->default(false);
            $table->boolean('comments_disabled')->default(false);
            $table->float('score')->default(0);
            $table->float('daily_score')->default(0);
            $table->float('weekly_score')->default(0);

            $table->timestamp('bumped_at')->nullable(); // Was just 'date'
            $table->timestamp('published_at')->nullable();

            $table->bigInteger('download_id')->nullable();
            $table->tinyText('download_type')->nullable();

            $table->bigInteger('last_user_id')->unsigned()->nullable();
            $table->foreign('last_user_id')->references('id')->on('users')->nullOnDelete();

            $table->boolean('has_download')->default(false);
            $table->boolean('approved')->nullable()->default(true);

            $table->unsignedMediumInteger('allowed_storage')->nullable();

            // These are more general table tracking dates.
            // They can be used, but bumped_at should be used for ordering so we don't bump a mod for every little edit.
            // To explain further, created_at is when the mod was created. updated_at is when the mod data was updated, regardless of anything.
            // bumped_at is when the mod was updated well enough to deserve a bump in the list. published_at is when the mod was published for the first time.
            $table->timestamps();

            $table->index('published_at');
            $table->index('name');
            $table->index('score');
            $table->index('daily_score');
            $table->index('weekly_score');
            $table->index('views');
            $table->index('downloads');
            $table->index('updated_at');
            $table->index('user_id');
            $table->index('game_id');
            $table->index('category_id');
            $table->index('bumped_at');
            $table->index('likes');
        });

        DB::statement('CREATE INDEX mods_name_trigram ON mods USING gist(name gist_trgm_ops);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP EXTENSION IF EXISTS pg_trgm');
        DB::statement('DROP INDEX IF EXISTS mods_name_trigram');

        Schema::dropIfExists('mods');
    }
}
