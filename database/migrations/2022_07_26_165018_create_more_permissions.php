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

        DB::table('permissions')->insert([
            'slug' => 'like-mod',
            'name' => 'Give a like to a mod'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'create-comment',
            'name' => 'Reply to threads or post comments'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-thread',
            'name' => 'Manage Threads'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'ban-user',
            'name' => 'Ban Users'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-game',
            'name' => 'Manage Games'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-category',
            'name' => 'Manage Categories'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-roles',
            'name' => 'Manage Roles'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-tag',
            'name' => 'Manage Tags'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-user',
            'name' => 'Manage Users'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'edit-forum-category',
            'name' => 'Manage Forum Categories'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'delete-own-mod-comment',
            'name' => 'Delete Own Mod Comments'
        ]);

        DB::table('permissions')->insert([
            'slug' => 'create-thread',
            'name' => 'Post threads'
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
