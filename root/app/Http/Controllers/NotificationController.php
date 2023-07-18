<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\APIService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Notification::class, 'notification');
    }

    public function unseenCount()
    {
        return APIService::getUnseenNotifications();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
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
     * @return Response
     */
    public function show(Notification $notification)
    {
        return $notification;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
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
     * @return Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
    }

    public function deleteAllNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->delete();
    }

    public function deleteReadNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->where('seen', true)->delete();
    }

    public function readAllNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->where('seen', false)->update([
            'seen' => true
        ]);
    }
}
