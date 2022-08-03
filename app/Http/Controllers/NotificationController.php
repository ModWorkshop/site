<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Notification::class, 'notification');
    }

    public function unseenCount(Request $request)
    {
        $userId = $request->user()->id;

        return Notification::where('user_id', $userId)->where('seen', false)->count();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->val([
            'unseen_only' => 'boolean',
        ]);

        $userId = $request->user()->id;

        $notifications = Notification::queryGet($val, function(Builder $query, array $val) use ($request, $userId) {
            $query->where('user_id', $userId);
            $query->orderByDesc('id');
            if (isset($val['unseen_only'])) {
                $query->where('seen', false);
            }
        });
        
        return NotificationResource::collection($notifications);
    }

    /**
     * Display the specified resource.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        return $notification;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        $val = $request->validate([
            'seen' => 'boolean',
        ]);

        $notification->update($val);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
    }
}
