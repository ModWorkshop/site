<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Link;
use App\Models\Mod;
use App\Services\APIService;
use File;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Services\ModService;
use Illuminate\Http\Response;

/**
 * @group Links
 */
class LinkController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(Link::class, Mod::class);
    }

    /**
     * List mod links
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        return BaseResource::collectionResponse($mod->links()->queryGet($request->val(), function($query, $val) use ($mod) {
            $query->with('user');
            if ($mod->download_type == 'link' && isset($mod->download_id)) {
                $query->orderByRaw("(CASE WHEN id = $mod->download_id THEN 0 ELSE 1 END) ASC, display_order DESC, updated_at DESC");
            } else {
                $query->orderByRaw("display_order DESC, updated_at DESC");
            }
        }));
    }

    /**
     * Create a link
     *
     * @authenticated
     */
    public function store(Request $request, Mod $mod)
    {
        return $this->update($request, $mod);
    }

    /**
     * Get a Link
     */
    public function show(Link $link)
    {
        return $link;
    }

    /**
     * Update a link
     *
     * @authenticated
     */
    public function update(Request $request, Mod $mod, Link $link=null)
    {
        $val = $request->validate([
            'name' => 'required|min_strict:3|max:255',
            'url' => 'required|url|min:3|max:1000',
            'desc' => 'string|nullable|max:1000',
            'label' => 'string|nullable|max:100',
            'version' => 'string|nullable|max:255',
            'display_order' => 'integer|min:-1000|max:1000|nullable',
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
     * Register a download
     *
     * Registers a download for the link, doesn't let you 'download' it twice
     * It applies the download to the mod that the link belongs to.
     * Works with guests
     */
    public function registerDownload(Request $request, Link $link)
    {
        ModService::registerDownload($link);
    }

    /**
     * Delete a link
     *
     * @authenticated
     */
    public function destroy(Link $link)
    {
        $link->delete();
    }
}
