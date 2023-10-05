<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\Game;
use App\Models\Report;
use Log;
use Request;

/**
 * @group Reports
 *
 * @authenticated
 */
class ReportController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(Report::class);
    }

    /**
     * Get List of Reports
     */
    public function index(FilteredRequest $request, Game $game=null)
    {
        $val = $request->val([
            'all' => 'boolean|nullable'
        ]);

        return BaseResource::collectionResponse(Report::queryGet($val, function($q, $val) use($game) {
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
     * Edit Report
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
     * Delete Report
     */
    public function destroy(Report $report)
    {
        $report->delete();
    }
}
