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
            $table->foreign('thumbnail_id')->references('id')->on('images')->onDelete('cascade');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->bigInteger('game_id')->unsigned()->nullable();
            $table->foreign('game_id')->references('id')->on('categories');
            $table->bigInteger('submitter_uid')->unsigned();
            $table->foreign('submitter_uid')->references('id')->on('users');
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
            $table->tinyText('banner')->default('');
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
            $table->timestamp('bump_date')->nullable(); // Was just 'date'
            $table->timestamp('publish_date')->nullable();

            // These are more general table tracking dates.
            // They can be used, but bump_date should be used for ordering so we don't bump a mod for every little edit.
            $table->timestamps();

            $table->index([
                'category_id',
                'game_id',
                'submitter_uid',
                'name',
                'bump_date', 
                'publish_date'
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
        Schema::dropIfExists('mods');
    }
}
