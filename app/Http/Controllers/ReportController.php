<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Models\Game;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Log;

class ReportController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Report::class, 'report');
    }
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'all' => 'boolean|nullable'
        ]);

        return JsonResource::collection(Report::queryGet($val, function($q, $val) use($game) {
            $q->orderByDesc('created_at');
            if (isset($game)) {
                $q->where('game_id', $game->id);
            } else {
                if (!($val['all'] ?? false)) {
                    $q->whereNull('game_id');
                }
            }
        }));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        $val = $request->validate([
            'archived' => 'boolean|required'
        ]);
        
        $report->update([
            'archived' => $val['archived']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        $report->delete();
    }
}
