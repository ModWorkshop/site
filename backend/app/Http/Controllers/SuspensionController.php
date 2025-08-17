<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\Suspension;
use Arr;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;

/**
 * @group Suspenions
 *
 * @authenciated
 */
class SuspensionController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Suspension::class);
    }

    /**
     * List suspensions
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'mod_id' => 'int|min:0|nullable|exists:mods,id',
        ]);

        $query = Arr::pull($val, 'query');

        return BaseResource::collectionResponse(Suspension::queryGet($val, function($q, array $val) use($query, $game) {
            $q->with('mod');
            $q->orderByDesc('created_at');

            if (isset($val['query']) || isset($game)) {
                $q->whereRelation('mod', function($q) use($val, $game) {
                    if (isset($val['query'])) {
                        $q->where('name', $val['name']);
                    }
                    if (isset($game)) {
                        $q->where('game_id', $game->id);
                    }
                });
            }

            if (isset($val['mod_id'])) {
                $q->where('mod_id', $val['mod_id']);
            }

            if (isset($val['user_id']) || isset($query)) {
                $q->whereRelation('mod', function($q) use ($val, $query) {
                    if (isset($val['user_id'])) {
                        $q-> where('user_id', $val['user_id']);
                    }
                    if (isset($query)) {
                        $q->where('name', 'ILIKE', '%'.$query.'%');
                    }
                });
            }
        }));
    }

    /**
     * Get a suspension
     */
    public function show(Suspension $suspension)
    {
        return $suspension;
    }

    /**
     * Update a suspension
     */
    public function update(Request $request, Suspension $suspension)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000',
            'status' => 'boolean|nullable',
        ]);

        $val['status'] ??= $suspension->status;
        
        $suspension->update($val);
    }

    /**
     * Delete a suspension
     */
    public function destroy(Suspension $suspension)
    {
        $mod = $suspension->mod;
        $suspension->delete();

        if ($mod->status) {
            if (!$mod->lastSuspension()->exists()) {
                $mod->update([
                    'suspended' => false
                ]);
            }
        }
    }
}
