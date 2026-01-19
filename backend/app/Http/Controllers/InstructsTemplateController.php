<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\InstructsTemplate;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\AuditLog;
use Illuminate\Http\Response;

/**
 * @group Instructions Templates
 */
class InstructsTemplateController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(InstructsTemplate::class);
    }
    /**
     * List instructions templates
     *
     * @authenticated
     */
    public function index(Game $game, FilteredRequest $request)
    {
        return BaseResource::collectionResponse(InstructsTemplate::queryGet($request->val(), function($q) use ($game) {
            $q->where('game_id', $game->id);
        }));
    }

    /**
     * Create an instructions template
     *
     * @authenticated
     */
    public function store(Request $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Get an instructions template
     */
    public function show(Game $game, InstructsTemplate $instructsTemplate)
    {
        return $instructsTemplate;
    }

    /**
     * Update an instructions template
     *
     * @authenticated
     */
    public function update(Request $request, Game $game, InstructsTemplate $instructsTemplate=null)
    {
        $val = $request->validate([
            'name' => 'string|min_strict:3|max:150',
            'instructions' => 'string|nullable|max:30000',
            'localized' => 'boolean|nullable',
        ]);

        if (isset($instructsTemplate)) {
            AuditLog::logUpdate($instructsTemplate, $val);
            $instructsTemplate->update($val);
        } else {
            $val['game_id'] = $game->id;
            $instructsTemplate = InstructsTemplate::create($val);
            AuditLog::logCreate($instructsTemplate, $val);
        }

        return $instructsTemplate;
    }

    /**
     * Delete an instructions template
     *
     * @authenticated
     */
    public function destroy(InstructsTemplate $instructsTemplate)
    {
        $instructsTemplate->delete();
    }
}
