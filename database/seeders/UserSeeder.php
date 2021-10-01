<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('users')->where('luffydafloffi@gmail.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'luffy',
                'email' => 'luffydafloffi@gmail.com',
                'password' => bcrypt('1234'),
            ]);
        }
    }
}
