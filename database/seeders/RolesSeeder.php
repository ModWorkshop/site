<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('roles')->where('name', 'Member')->exists()) {
            DB::table('roles')->insert([
                'name' => 'Member',
                'desc' => 'The global role that every registered member has. You cannot remove the role from registered users.',
                'order' => -1
            ]);
    
            DB::table('roles')->insert([
                'name' => 'Admin',
                'tag' => 'Admin',
                'desc' => 'The administrator is the highest role in the site, people with this role are able to do pretty much most things.',
                'order' => 1
            ]);
    
            DB::table('roles')->insert([
                'name' => 'Site Moderator',
                'tag' => 'Moderator',
                'desc' => 'The site moderator is 2nd to the admin, they are able to do a lot, but not everything.',
                'order' => 2
            ]);

            DB::table('permissions')->insert([
                'slug' => 'edit-mod',
                'name' => 'Edit Mods'
            ]);
            
            DB::table('permissions')->insert([
                'slug' => 'change-avatar',
                'name' => 'Changing Avatars'
            ]);

            // Give Luffy admin
            DB::table('role_user')->insert([
                'role_id' => 2,
                'user_id' => 1
            ]);

            // Give members perms
            DB::table('permission_role')->insert([
                'permission_id' => 1,
                'role_id' => 1
            ]);

            DB::table('permission_role')->insert([
                'permission_id' => 2,
                'role_id' => 1
            ]);
        }
        
    }
}
