<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Models\Game;
use App\Models\Report;
use Illuminate\Http\Request;

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
     * List reports
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
     * Update a report
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
     * Delete a report
     */
    public function destroy(Report $report)
    {
        $report->delete();
    }
}
