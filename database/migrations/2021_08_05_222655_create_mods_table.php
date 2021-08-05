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

            $table->bigInteger('cid')->unsigned();
            $table->foreign('cid')->references('id')->on('categories');
            $table->bigInteger('root')->unsigned();
            $table->foreign('root')->references('id')->on('categories');
            $table->bigInteger('submitter_uid')->unsigned();
            $table->foreign('submitter_uid')->references('id')->on('users');
            $table->bigInteger('instid')->default('');
            //$table->foreign('instid')->references('id')->on('instructions'); //TODO: make table

            $table->tinyText('name');
            $table->text('desc'); // Was description
            $table->tinyText('short_desc')->default(''); // Was short_description
            $table->text('changelog')->default('');
            $table->text('license')->default('');
            $table->text('instructions')->default('');
            $table->json('depends_on')->nullable();
            $table->tinyInteger('visibility'); // Was hidden
            $table->tinyText('thumbnail')->default(''); // Previews will be split into a table when migrating from old
            $table->tinyText('banner')->default('');
            $table->string('url')->default('');
            $table->bigInteger('downloads');
            $table->bigInteger('views');
            $table->tinyText('version')->default('');
            $table->tinyText('donation')->default(''); // Was receiver_email
            $table->string('collaborators')->default('');
            $table->string('invited')->default('');
            $table->boolean('suspended_status');
            $table->boolean('comments_disabled');
            $table->tinyInteger('file_status');
            $table->float('score');

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
