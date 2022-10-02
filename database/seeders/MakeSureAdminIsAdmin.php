<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MakeSureAdminIsAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::where('name', 'Admin')->first();

        User::where('id', 1)->first()->roles()->attach($admin->id);
        Role::where('name', 'Admin')->first()->permissions()->attach(Permission::where('name', 'admin')->first()->id);
    }
}
