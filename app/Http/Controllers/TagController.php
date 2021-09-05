<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * @group tag
 */
class TagController extends Controller
{
    /**
     * Mod Tags
     * 
     * Returns a list of mod tags
     *
     * @param Request $request
     * @return void
     */
    public function getTags(Request $request)
    {
        $val = $request->validate([
            'limit' => 'integer|min:1|max:100|default:50',
        ]);

        return Tag::limit($val['limit'])->get();
    }
}
