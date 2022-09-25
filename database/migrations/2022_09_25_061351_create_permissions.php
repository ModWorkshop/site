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
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-discussions',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-discussions',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-games',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-users',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-categories',
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-categories',
        ]);

        DB::table('permissions')->insert([
            'name' => 'delete-own-mod-comments',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-forum-categories',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-mods',
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-mods',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-roles',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-instructions',
        ]);

        DB::table('permissions')->insert([
            'name' => 'manage-tags',
        ]);
        
        DB::table('permissions')->insert([
            'name' => 'manage-documents',
        ]);

        DB::table('permissions')->insert([
            'name' => 'like-mods',
        ]);

        DB::table('permissions')->insert([
            'name' => 'create-reports',
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
