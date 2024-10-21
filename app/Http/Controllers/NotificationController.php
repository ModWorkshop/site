<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilteredRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\APIService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Response;

/**
 * @group Notifications
 * @authenticated
 */
class NotificationController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Notification::class, 'notification');
    }

    /**
     * List notifications
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

        return NotificationResource::collectionResponse($notifications);
    }

    /**
     * Get a notification
     */
    public function show(Notification $notification)
    {
        return $notification;
    }

    /**
     * Update notification
     */
    public function update(Request $request, Notification $notification)
    {
        $val = $request->validate([
            'seen' => 'boolean',
        ]);

        $notification->update($val);
    }

    /**
     * Get unseen notifications count
     */
    public function unseenCount()
    {
        return APIService::getUnseenNotifications();
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
    }

    /**
     * Delete all notifications
     */
    public function deleteAllNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->delete();
    }

    /**
     * Delete all read notifications
     */
    public function deleteReadNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->where('seen', true)->delete();
    }

    /**
     * Read all notifications
     */
    public function readAllNotifications(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->where('seen', false)->update([
            'seen' => true
        ]);
    }
}
