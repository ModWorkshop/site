<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('roles')->where('name', 'Member')->exists()) {
            DB::table('roles')->updateOrInsert([
                'name' => 'Member',
                'desc' => 'The global role that every registered member has. You cannot remove the role from registered users.',
                'order' => -1
            ]);

            DB::table('roles')->updateOrInsert([
                'name' => 'Admin',
                'desc' => 'The administrator is the highest role in the site, people with this role are able to do pretty much most things.',
                'tag' => 'Admin',
                'order' => 1
            ]);
        }
        
        Permission::query()->delete();

        DB::table('permissions')->updateOrInsert([
            'name' => 'admin',
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'moderate-users',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'create-discussions',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-discussions',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-games',
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-users',
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-categories',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'create-categories',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'delete-own-mod-comments',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-forum-categories',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-mods',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'create-mods',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-roles',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-instructions',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-tags',
            'type' => null
        ]);
        
        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-documents',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'like-mods',
            'type' => null
        ]);

        DB::table('permissions')->updateOrInsert([
            'name' => 'create-reports',
            'type' => null
        ]);
        
        //The only game-specific permission. It's like the 'admin' of games.
        DB::table('permissions')->updateOrInsert([
            'name' => 'manage-game',
            'type' => 'game'
        ]);

        Role::where('name', 'Admin')->first()->permissions()->attach(Permission::where('name', 'admin')->first()->id);
    }
}
