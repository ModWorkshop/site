<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\TrackSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @group Track Session
 */
class TrackSessionController extends Controller
{
    public function __construct() {
        $this->authorizeGameResource(TrackSession::class);
    }

    /**
     * List tracked sessions
     */
    public function index(Request $request)
    {
        $val = $request->validate([
            'ip_address' => 'string|nullable|max:150',
            'user_id' => 'int|min:1|nullable|exists:users,id',
        ]);

        return BaseResource::collectionResponse(TrackSession::queryGet($val, function($q, $val) {
            $q->with(['user']);
            $q->where('updated_at', '>', Carbon::now()->subMonths(3));
            $q->whereNotNull('user_id'); // Not really much point seeing guests ig
            if (!empty($val['ip_address'])) {
                $q->where('ip_address', $val['ip_address']);
            }
            if (isset($val['user_id'])) {
                $q->where('user_id', $val['user_id']);
            }
            $q->orderByDesc('updated_at');
        }));
    }
}
