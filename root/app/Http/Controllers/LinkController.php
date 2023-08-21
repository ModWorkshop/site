<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Link;
use App\Models\Mod;
use App\Services\APIService;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * @group Mods
 * 
 * @subgroup Links
 */
class LinkController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Link::class, Mod::class);
    }

    /**
     * Get List of Mod Links
     *
     * @return Response
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return JsonResource::collection($mod->links()->queryGet($request->val()));
    }

    /**
     * Create Link
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        return $this->update($request, $mod);
    }

    /**
     * Get Link
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Link $link)
    {
        return $link;
    }

    /**
     * Edit Link
     * 
     * @authenticated
     */
    public function update(Request $request, Mod $mod, Link $link=null)
    {
        $val = $request->validate([
            'name' => 'required|min:3|max:255',
            'url' => 'required|url|min:3|max:1000',
            'desc' => 'string|nullable|max:1000',
            'label' => 'string|nullable|max:100',
            'version' => 'string|nullable|max:255',
            'image_id' => 'int|nullable|exists:images,id'
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        $user = $request->user();
        $mod = isset($link) ? $link->mod : $mod;

        if (isset($link)) {
            if (isset($val['version']) && $link->version !== $val['version']) {
                $mod->bump(true);
            }
            $link->update($val);
        } else {
            $val['user_id'] = $user->id;
            $link = $mod->links()->create($val);
            $mod->bump(false);
            $mod->refresh();
            $mod->calculateFileStatus();
        }

        return $link;
    }

    /**
     * Delete Link
     *
     * @authenticated
     */
    public function destroy(Link $link)
    {
        $link->delete();
    }
}
