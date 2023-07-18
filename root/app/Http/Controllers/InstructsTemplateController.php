<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\InstructsTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class InstructsTemplateController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(InstructsTemplate::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Game $game, FilteredRequest $request)
    {
        return JsonResource::collection(InstructsTemplate::queryGet($request->val(), function($q) use ($game) {
            $q->where('game_id', $game->id);
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, Game $game)
    {
        return $this->update($request, $game);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Game $game, InstructsTemplate $instructsTemplate)
    {
        return $instructsTemplate;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(InstructsTemplate $instructsTemplate)
    {
        $instructsTemplate->delete();
    }
}
