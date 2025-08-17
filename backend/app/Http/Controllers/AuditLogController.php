<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AuditLogResource;
use App\Models\Game;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(AuditLog::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilteredRequest $request, ?Game $game=null)
    {
        $val = $request->val();

        return AuditLogResource::collectionResponse(AuditLog::queryGet($val, function($query) use ($game) {
            if (isset($game)) {
                $query->where('game_id', $game->id);
            } 
            $query->orderBy('updated_at', 'desc');
        }));
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLog $auditLog)
    {
        return $auditLog;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuditLog $auditLog)
    {
        \Log::info('hi');
        $auditLog->delete();
    }
}
