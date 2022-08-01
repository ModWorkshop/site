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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilteredRequest $request)
    {
        $val = $request->validate([
            'unseen_only' => 'boolean',
        ]);
        $notifications = Notification::queryGet($val, function(Builder $query, array $val) use ($request) {
            $query->where('user_id', $request->user()->id);
            $query->orderByDesc('id');
            if (isset($val['unseen_only'])) {
                $query->where('seen', false);
            }
        });
        
        $resource = NotificationResource::collection($notifications);
        $resource->additional(['total_unseen' => Notification::where('seen', false)->count()]);

        return $resource;
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
