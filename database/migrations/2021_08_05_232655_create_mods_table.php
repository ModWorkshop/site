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
            $table->bigInteger('downloads')->default(0);
            $table->bigInteger('views')->default(0);
            $table->tinyText('version')->default('');
            $table->tinyText('donation')->default(''); // Was receiver_email
            
            /**
             * Was collaborators and invited
             * We'll handle this with a link table that will be able to assign type of collaborator
             * Credit, Editor, Maintainer for example
             * Why the value? At the moment I don't think there is a quick and easy way to do an if statement
             * and if the mod isn't visible try to look for joined tables... you can understand it's a little tricky.
             */
            $table->string('access_ids')->default('');
            $table->boolean('suspended')->default(false);
            $table->boolean('comments_disabled')->default(false);
            $table->tinyInteger('file_status')->default(0);
            $table->float('score')->default(0);
            $table->timestamp('bump_date')->nullable(); // Was just 'date'
            $table->timestamp('publish_date')->nullable();

            // These are more general table tracking dates.
            // They can be used, but bump_date should be used for ordering so we don't bump a mod for every little edit.
            $table->timestamps();
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
