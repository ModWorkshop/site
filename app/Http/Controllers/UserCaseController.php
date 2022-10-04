<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\UserCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCaseController extends Controller
{
    public function __construct(Request $request)
    {
        $this->authorizeGameResource(UserCase::class, 'user_case');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Game $game=null)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'all' => 'in:true,false|nullable'
        ]);

        return JsonResource::collection(UserCase::with('modUser')->queryGet($val, function($q, $val) use($game) {
            if (isset($game)) {
                $q->where('game_id', $game->id);
            } else {
                if (!($val['all'] ?? false)) {
                    $q->whereNull('game_id');
                }
            }

            if (isset($val['user_id'])) {
                $q->where('user_id', $val['user_id']);
            }
        }));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game)
    {
        $val = $request->validate([
            'user_id' => 'int|min:0|nullable|exists:users,id',
            'reason' => 'string|min:3|max:1000',
            'expire_date' => 'date|required|nullable|after:now'
        ]);

        $val['game_id'] = $game->id;
        $val['mod_user_id'] = $this->userId();
        $val['warning'] = true;

        UserCase::create($val);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserCase $userCase)
    {
        return $userCase;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserCase $userCase)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000',
            'pardon_reason' => 'string|max:1000',
            'expire_date' => 'date|nullable|after:now',
            'pardoned' => 'boolean'
        ]);

        $userCase->update($val);

        if (!$userCase->warning) {
            if ($userCase->expire_date < Carbon::now() || $userCase->pardoned) {
                $userCase->ban()->delete();
            } else {
                if (!isset($userCase->ban)) {
                    $userCase->ban()->create([
                        'user_id' => $userCase->user_id
                    ]);
                }
            }
        }

        return $userCase;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserCase $userCase)
    {
        $userCase->delete();
    }
}
