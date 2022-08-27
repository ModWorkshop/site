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
        Schema::create('mods', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('thumbnail_id')->unsigned()->nullable();
            $table->foreign('thumbnail_id')->references('id')->on('images');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('categories');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            //$table->bigInteger('instid')->default(0); Implement later
            //$table->foreign('instid')->references('id')->on('instructions'); //TODO: make table

            $table->tinyText('name');
            $table->text('desc'); // Was description
            $table->tinyText('short_desc')->default(''); // Was short_description
            $table->text('changelog')->default('');
            $table->text('license')->default('');
            $table->text('instructions')->default('');
            $table->json('depends_on')->nullable();
            $table->tinyInteger('visibility')->default(0); // Was hidden
            $table->tinyText('legacy_banner_url')->default(''); //Only to be used for old mods, all new mods will have to move to banner_id.
            $table->string('url')->default('');
            $table->bigInteger('downloads')->unsigned()->default(0);
            $table->bigInteger('likes')->unsigned()->default(0);
            $table->bigInteger('views')->unsigned()->default(0);
            $table->tinyText('version')->default('');
            $table->tinyText('donation')->default(''); // Was receiver_email

            $table->boolean('suspended')->default(false);
            $table->boolean('comments_disabled')->default(false);
            $table->tinyInteger('file_status')->default(0);
            $table->float('score')->default(0);
            $table->timestamp('bumped_at')->nullable(); // Was just 'date'
            $table->timestamp('published_at')->nullable();

            $table->bigInteger('download_id')->nullable();
            $table->tinyText('download_type')->nullable();

            $table->bigInteger('banner_id')->unsigned()->nullable();
            $table->foreign('banner_id')->references('id')->on('images');
            $table->bigInteger('likes')->unsigned()->default(0);

            $table->bigInteger('last_user_id')->unsigned()->nullable();
            $table->foreign('last_user_id')->references('id')->on('users');

            // These are more general table tracking dates.
            // They can be used, but bumped_at should be used for ordering so we don't bump a mod for every little edit.
            // To explain further, created_at is when the mod was created. updated_at is when the mod data was updated, regardless of anything.
            // bumped_at is when the mod was updated well enough to deserve a bump in the list. published_at is when the mod was published for the first time.
            $table->timestamps();

            $table->index([
                'category_id',
                'game_id',
                'user_id',
                'name',
                'bumped_at', 
                'published_at'
            ]);
            $table->index('name');
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
        Schema::dropIfExists('mods');
    }
}
