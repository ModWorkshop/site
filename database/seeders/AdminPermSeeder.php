<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AdminPermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('permissions')->where('name', 'Admin')->exists()) {
            DB::table('permissions')->insert([
                'slug' => 'admin',
                'name' => 'Admin'
            ]);
        }
    }
}
