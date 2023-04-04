<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Document;
use App\Models\Game;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Document::class, 'document');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        return JsonResource::collection(Document::queryGet($request->val(), function($q) use($game) {
            if (isset($game)) {
                $q->where('game_id', $game->id);
            } else {
                $q->whereNull('game_id');
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document=null)
    {
        $val = $request->validate([
            'name' => 'string|min:3|max:150',
            'desc' => 'string|min:3|max:30000',
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $document->delete();
    }
}
