<?php

namespace App\Http\Controllers;

use App\Models\Mod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * @group Mod
 * 
 * API routes for interacting with mods specifically
 */
class ModController extends Controller
{
    /**
     * Mod
     * 
     * Returns a single mod
     * 
     * @urlParam mod integer required The ID of the mod
     *
     * @param Mod $mod
     * @return void
     */
    public function getMod(Mod $mod)
    {
        return $mod->toJson();
    }

    /**
     * Mods
     * 
     * Returns many mods, has a few options for searching the right mods
     *
     * @param Request $request
     * @return void
     */
    public function getMods(Request $request)
    {
        // Query parameters
        $val = $request->validate([
            // How many mods should this return. 
            'limit' => 'integer|min:1|max:50',
            'tags' => 'array',
            'tags.*' => 'integer|min:1',
            'notInTags' => 'array',
            // Filter out mods that are in these tags
            'notInTags.*' => 'integer|min:1'
        ]);
        
        $query = Mod::limit($val['limit'] ?? 40)->list()->orderBy('updated_at', 'DESC');
        
        if (isset($val['tags'])) {
            $query->whereHas('tags', function(Builder $q) use ($val) {
                $q->whereIn('tags.id', $val['tags']);
            });
        }

        
        if (isset($val['notInTags'])) {
            $query->whereDoesntHave('tags', function(Builder $q) use ($val) {
                $q->whereIn('tags.id', $val['notInTags']);
            });
        }

        return $query->get();
    }

    /**
     * Create Mod
     * 
     * Creates a new mod
     * 
     * @authenticated
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        return $this->update($request);
    }
    
    /**
     * Update Mod
     * 
     * Updates data of a mod
     * 
     * @authenticated
     * 
     * @param Request $request
     * @param Mod|null $mod
     * @return void
     */
    public function update(Request $request, Mod $mod=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150|required',
            'desc' => 'string|max:30000|required',
            'version' => 'string|max:100',
            'visibility' => 'integer|min:1|max:4|required',
            'game_id' => 'integer|min:1|required|exists:categories,id',
            'category_id' => 'integer|min:1|nullable|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'integer|min:1',
        ]);

        $tags = Arr::pull($val, 'tags'); // Since 'tags' isn't really inside the model, we need to pull it out.

        if (isset($mod)) {
            $mod->update($val);
        } else {
            // Never put something like $request->all(); in create.
            //Laravel may have guard for this, but there's really no reason what to so ever to give it that.
            $val['submitter_uid'] = $request->user()->id;
            $mod = Mod::create($val); // Validate handles the important stuff already.
        }


        if(isset($tags)) {
            $mod->tags()->sync($tags);
        }

        return $mod->toJson();
        
        return back()->withErrors([
            'name' => 'A mod must have a name and it should not exceed 150 characters',
            'desc' => 'A mod must have a description and it should not exceed 30k character'
        ]);
    }
}
