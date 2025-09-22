<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->success('Notifications retrieved successfully', NotificationResource::collection($notifications));
        } catch (Exception $e) {
            return response()->errorResponse('Failed to fetch notification', [], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $user = $request->user();

            $notification = $user->notifications()->where('id', $id)->firstOrFail();

            $notification->markAsRead();

            return response()->success('Notifications retrieved successfully', new NotificationResource($notification));
        } catch (Exception $e) {
            return response()->errorResponse('Failed to fetch Notification', [], 500);
        }
    }
}