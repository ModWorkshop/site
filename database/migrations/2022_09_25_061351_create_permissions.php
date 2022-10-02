<?php

use App\Models\Permission;
use App\Models\Role;
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
        Permission::query()->delete();

        DB::table('permissions')->insert([
            'name' => 'admin',
        ]);

        DB::table('permissions')->insert([
            'name' => 'moderate-users',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-discussions',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-discussions',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-games',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-users',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-categories',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-categories',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'delete-own-mod-comments',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-forum-categories',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-mods',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-mods',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-roles',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-instructions',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-tags',
            'type' => null
        ]);
        
        DB::table('permissions')->insert([
            'name' => 'manage-documents',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'like-mods',
            'type' => null
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-reports',
            'type' => null
        ]);
        
        //The only game-specific permission. It's like the 'admin' of games.
        DB::table('permissions')->insert([
            'name' => 'manage-game',
            'type' => 'game'
        ]);

        Role::where('name', 'Admin')->first()->permissions()->attach(Permission::where('name', 'admin')->first()->id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::query()->delete();
    }
};
