<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Mod;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ModsTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Tag::factory()->count(100)->create();
        // Mod::factory()->count(25000)->create()->each(function(Mod $mod)
        // {
        //     $tags = $mod->tags();
        //     $tags->attach(rand(1, 100));
        //     $tags->attach(rand(1, 100));
        //     $tags->attach(rand(1, 100));
        //     $tags->attach(rand(1, 100));
        // });
        Category::factory()->count(10)->create()->each(function(Category $category) {
            Category::factory()->count(10)->create()->each(function(Category $childCategory) use ($category) {
                $childCategory->update([
                    'game_id' => $category->id,
                    'parent_id' => $category->id
                ]);
            });
        });
    }
}
