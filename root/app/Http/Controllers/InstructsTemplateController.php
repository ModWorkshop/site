<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\InstructsTemplate;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;

/**
 * @group Instructions Templates
 *
 * @authenticated
 */
class InstructsTemplateController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(InstructsTemplate::class);
    }
    /**
     * Get list of Instructions Templates
     */
    public function index(Game $game, FilteredRequest $request)
    {
        return BaseResource::collectionResponse(InstructsTemplate::queryGet($request->val(), function($q) use ($game) {
            $q->where('game_id', $game->id);
        }));
    }

    /**
     * Create Instructions Template
     *
     * @authenticated
     */
    public function store(Request $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Get Instructions Template
     */
    public function show(Game $game, InstructsTemplate $instructsTemplate)
    {
        return $instructsTemplate;
    }

    /**
     * Edit Instructions Template
     *
     * @authenticated
     */
    public function update(Request $request, Game $game, InstructsTemplate $instructsTemplate=null)
    {
        $val = $request->validate([
            'name' => 'string|max:150',
            'instructions' => 'string|nullable|max:30000',
            'localized' => 'boolean|nullable',
        ]);

        if (isset($instructsTemplate)) {
            $instructsTemplate->update($val);
        } else {
            $val['game_id'] = $game->id;
            $instructsTemplate = InstructsTemplate::create($val);
        }

        return $instructsTemplate;
    }

    /**
     * Delete Instructions Template
     *
     * @authenticated
     */
    public function destroy(InstructsTemplate $instructsTemplate)
    {
        $instructsTemplate->delete();
    }
}
