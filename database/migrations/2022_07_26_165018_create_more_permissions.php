<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            'slug' => 'edit-own-mod',
            'name' => 'Edit Own Mod'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'delete-mod',
            'name' => 'Delete Any Mod'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'suspend-mod',
            'name' => 'Suspend Mod'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'delete-comment',
            'name' => 'Delete Any Comment'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-comment',
            'name' => 'Edit Any Comment'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-own-comment',
            'name' => 'Edit Own Comment'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
