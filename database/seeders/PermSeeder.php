<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('permissions')->where('slug', 'admin')->exists()) {
            DB::table('permissions')->insert([
                'slug' => 'change-avatar',
                'name' => 'Change Avatars'
            ]);
            DB::table('permissions')->insert([
                'slug' => 'edit-mod',
                'name' => 'Edit & Create mods',
                'desc' => 'This does not cover deleting mods, users should have the right to delete their content.'
            ]);
            DB::table('permissions')->insert([
                'slug' => 'admin',
                'name' => 'Administrator',
                'desc' => 'The bypass all permission, this permission gives the user pretty much the ability to do most things.'
            ]);

            // 
            /**
             * Grant members change-avatar and edit-mod permission
             * This will be improved of course, we'll have function that would be something like:
             * role->grantPermission('edit-mod'); 
             * role->denyPermission('edit-mod')
             * role->removePermission('edit-mod');
             * Since allow is by default 1, we don't need to define it.
             */
            DB::table('permissions_roles_links')->insert([
                'perm_id' => 1,
                'role_id' => 1
            ]);

            DB::table('permissions_roles_links')->insert([
                'perm_id' => 2,
                'role_id' => 1
            ]);
        }
    }
}
