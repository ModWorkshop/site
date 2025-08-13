<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnableFuzzystrmatchExtension extends Migration
{
    public function up()
    {
        // Enable the fuzzystrmatch extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS fuzzystrmatch;');
    }

    public function down()
    {
        // Remove the extension if rolling back
        DB::statement('DROP EXTENSION IF EXISTS fuzzystrmatch;');
    }
}