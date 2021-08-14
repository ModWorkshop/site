<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('categories')->where('name', 'PAYDAY 2')->exists()) {
            DB::table('categories')->insert([
                'name' => 'PAYDAY 2',
                'short_name' => 'payday-2',
                'last_date' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
}
