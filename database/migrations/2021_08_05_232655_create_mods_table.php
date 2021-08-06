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

            $table->bigInteger('cid')->unsigned()->nullable();
            $table->foreign('cid')->references('id')->on('categories');
            $table->bigInteger('root')->unsigned()->nullable();
            $table->foreign('root')->references('id')->on('categories');
            $table->bigInteger('submitter_uid')->unsigned();
            $table->foreign('submitter_uid')->references('id')->on('users');
            $table->bigInteger('instid')->default(0);
            //$table->foreign('instid')->references('id')->on('instructions'); //TODO: make table

            $table->tinyText('name');
            $table->text('desc'); // Was description
            $table->tinyText('short_desc')->default(''); // Was short_description
            $table->text('changelog')->default('');
            $table->text('license')->default('');
            $table->text('instructions')->default('');
            $table->json('depends_on')->nullable();
            $table->tinyInteger('visibility')->default(0); // Was hidden
            $table->tinyText('thumbnail')->default(''); // Previews will be split into a table when migrating from old
            $table->tinyText('banner')->default('');
            $table->string('url')->default('');
            $table->bigInteger('downloads')->default(0);
            $table->bigInteger('views')->default(0);
            $table->tinyText('version')->default('');
            $table->tinyText('donation')->default(''); // Was receiver_email
            $table->string('collaborators')->default('');
            $table->string('invited')->default('');
            $table->boolean('suspended_status')->default(false);
            $table->boolean('comments_disabled')->default(false);
            $table->tinyInteger('file_status')->default(0);
            $table->float('score')->default(0);

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
