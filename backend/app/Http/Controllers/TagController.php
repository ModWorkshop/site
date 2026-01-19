<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\TagResource;
use App\Models\Game;
use App\Models\AuditLog;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

/**
 * @group Tags
 */
class TagController extends Controller
{
    public function __construct(Request $request) {
        $this->authorizeGameResource(Tag::class);
    }

    /**
     * List tags
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'game_id' => 'integer|min:1|nullable|exists:games,id',
            'type' => 'string|in:mod,forum',
            'show_hidden' => 'boolean|nullable',
            'global' => 'boolean|nullable'
        ]);

        $tags = Tag::queryGet($val, function($query, array $val) use($game) {
            $query->where(function($q) use ($val, $game) {
                $gameId = $game?->id ?? $val['game_id'] ?? null;
                if (isset($gameId)) {
                    $q->where('game_id', $gameId);
                }
                if (isset($val['global']) && $val['global']) {
                    $q->orWhereNull('game_id');
                    if (isset($gameId) && !($val['show_hidden'] ?? false)) {
                        $q->whereDoesntHaveIn('gamesHiding', function($q) use($gameId) { // Filters out tags that are hidden by the game
                            $q->where('game_hidden_tags.game_id', $gameId);
                        });
                    }
                }
                if (isset($val['type'])) {
                    $q->where(fn($q) => $q->where('type', $val['type'])->orWhere('type', '')->orWhereNull('type'));
                }
            });
        });

        return TagResource::collectionResponse($tags);
    }

    /**
     * Create a tag
     *
     * @authenticated
     */
    public function store(Request $request, Game $game=null)
    {
        return $this->update($request, $game);
    }

    /**
     * Get a tag
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    /**
     * Update a tag
     *
     * @authenticated
     */
    public function update(Request $request, Game $game=null, Tag $tag=null)
    {
        $val = $request->validate([
            'name' => 'string|required|min_strict:2|max:100',
            'color' => 'string|required|hex_color|max:8',
            'notice' => 'string|nullable|min_strict:3|max:1000',
            'notice_type' => 'string|nullable|in:info,warning,danger',
            'type' => 'string|nullable|in:all,forum,mod',
            'notice_localized' => 'boolean|nullable',
        ]);

        $val['type'] ??= '';
        $val['notice_type'] ??= 'info';
        $val['notice'] ??= '';

        if (isset($tag)) {
            AuditLog::logUpdate($tag, $val);
            $tag->update($val);
        } else {
            $val['game_id'] = $game?->id;
            $tag = Tag::create($val);

            AuditLog::logCreate($tag, $val);
        }

        return new TagResource($tag);
    }

    /**
     * Delete a tag
     *
     * @authenticated
     */
    public function destroy(Tag $tag)
    {
        AuditLog::logDelete($tag);
        $tag->delete();
    }
}
