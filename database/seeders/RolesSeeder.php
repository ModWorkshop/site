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
        }
        
    }
}
