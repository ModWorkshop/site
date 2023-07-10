<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Document;
use App\Models\Game;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class DocumentController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Document::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'get_unlisted' => 'boolean',
        ]);

        $user = $this->user();

        $manageDocs = $user->hasPermission('manage-documents');
        $manageDocsGame = $user->hasPermission('manage-documents', $game);

        return JsonResource::collection(Document::queryGet($val, function($q) use($game, $manageDocs, $manageDocsGame) {
            if (isset($game)) {
                $q->where('game_id', $game->id);
                if (!$manageDocsGame) {
                    $q->where('is_unlisted', false);
                }
            } else {
                $q->whereNull('game_id');
                if (!$manageDocs) {
                    $q->where('is_unlisted', false);
                }
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Game $game)
    {
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     */
    public function getDocument(Request $request, $document)
    {
        $query = Document::query();

        if (is_numeric($document)) {
            return $query->findOrFail($document);
        } else {
            return $query->where('url_name', $document)->firstOrFail();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'desc' => 'string|min:3|max:50000',
            'is_unlisted' => 'boolean|nullable',
            'game_id' => 'integer|nullable|min:1|exists:games,id',
            'url_name' => 'string|nullable|max:30|alpha_dash'
        ]);

        $val['last_user_id'] = $this->userId();

        $gameId = Arr::get($val, 'game_id');
        $urlName = Arr::get($val, 'url_name');

        if (isset($urlName) && (!isset($document) || $document->url_name !== $urlName)) {
            $queryCheck = Document::where('url_name', $urlName);
            if (isset($gameId)) {
                $queryCheck->where('game_id', $gameId);
            } else {
                $queryCheck->whereNull('game_id');
            }

            if ($queryCheck->exists()) {
                abort(409, 'URL Name already exists in the game');
            }

        }

        if (isset($document)) {
            Arr::pull($val, 'game_id'); //Don't allow game IDs to be set on updates
            $document->update($val);
        } else {
            $document = Document::create($val);
        }

        return $document;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
    }
}
