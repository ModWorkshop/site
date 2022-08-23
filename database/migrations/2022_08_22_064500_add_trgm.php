<?php

use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{

    /**

     * Run the migrations.

     *

     * @return void

     */

    public function up()

    {

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        DB::statement('CREATE INDEX mods_name_trigram ON mods USING gist(name gist_trgm_ops);');

    }


    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        DB::statement('DROP INDEX IF EXISTS mods_name_trigram');

        DB::statement('DROP EXTENSION IF EXISTS pg_trgm');

    }

};