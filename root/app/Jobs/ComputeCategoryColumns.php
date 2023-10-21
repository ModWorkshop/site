<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ComputeCategoryColumns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $categories;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $this->categories = Arr::keyBy(Category::all(), 'id');

        foreach ($this->categories as $id => $cat) {
            $cat->computed_breadcrumb = $this->makeBreadcrumb($cat);
            $cat->computed_children = $this->getChildren($cat);

            $cat->saveQuietly();
        }
    }

    function getChildren(Category $cat, array &$children = [])
    {
        foreach ($this->categories as $loopCat) {
            if ($loopCat->parent_id == $cat->id) {
                $children[] = $loopCat->id;
                $this->getChildren($loopCat, $children);
            }
        }
        return $children;
    }

    function makeBreadcrumb(Category $category=null, array $arr=[], array &$loopCheck=[]) : array {
        if (isset($category)) {
            if (!isset($loopCheck[$category->id])) {
                $loopCheck[$category->id] = true;
                $arr = [
                    ...$this->makeBreadcrumb($this->categories[$category->parent_id] ?? $category->parent, [[
                        'name' => $category->name,
                        'id' => $category->id,
                        'type' => 'category'
                    ]], $loopCheck),
                    ...$arr,
                ];
            } else {
                Log::alert('Category loop detected! Please look into the database.', $category->toArray());
            }
        }

        return $arr;
    }

}
