<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\AuditLog;
use App\Models\Game;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Webhook::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilteredRequest $request, ?Game $game=null)
    {
        return BaseResource::collectionResponse(Webhook::queryGet($request->val(), function($q) use ($game) {
            if (isset($game)) {
                $q->where('game_id', $game->id);
            } else {
                $q->whereNull('game_id');
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ?Game $game=null)
    {
        $val = $request->validate([
            'name' => 'max:120|min:3|required',
            'url' => 'max:1000|url|required',
            'event' => 'in:mod_approval,mod_approval_new,mod_deleted,mod_suspended,mod_published,report_new|required',
            'content' => 'max:1000|required'
        ]);

        if (isset($game)) {
            $val['game_id'] = $game->id;
        }

        return Webhook::create($val);
    }

    /**
     * Display the specified resource.
     */
    public function show(Webhook $webhook)
    {
        return $webhook;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ?Webhook $webhook=null)
    {
        $val = $request->validate([
            'name' => 'max:120|min:3|nullable',
            'url' => 'max:1000|url|nullable',
            'event' => 'in:mod_approval,mod_approval_new,mod_deleted,mod_suspended,mod_published,report_new|required',
            'content' => 'max:1000|nullable'
        ]);

        $webhook->update($val);

        return $webhook;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Webhook $webhook)
    {
        AuditLog::logDelete($webhook);

        $webhook->delete();
    }
}
