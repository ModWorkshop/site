<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
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
                'desc' => 'The administrator is the highest role in the site, people with this role are able to do pretty much most things.',
                'tag' => 'Admin',
                'order' => 1
            ]);

            // Give Root Admin role
            DB::table('role_user')->insert([
                'role_id' => 2,
                'user_id' => 1
            ]);

            Role::where('name', 'admin')->permissions()->attach(Permission::where('name', 'admin')->first()->id);
        }
        
    }
}
