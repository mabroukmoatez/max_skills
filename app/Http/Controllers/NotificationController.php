<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications()
    {
        $notifications = Notification::where('reciver_id', Auth::id())
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'status' => $notification->status,
                    'sender' => $notification->sender ? $notification->sender->name : 'System',
                    'created_at' => $notification->created_at,
                    'link' => '#' // You can customize this based on notification type
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->where('status', 0)->count()
        ]);
    }

    /**
     * Mark notifications as read
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        // Mark all as read
        if ($request->input('all', false)) {
            Notification::where('reciver_id', Auth::id())
                ->where('status', 0)
                ->update(['status' => 1]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }
        
        // Mark specific notification as read
        $notificationId = $request->input('notification_id');
        if ($notificationId) {
            $notification = Notification::where('id', $notificationId)
                ->where('reciver_id', Auth::id())
                ->first();
                
            if ($notification) {
                $notification->status = 1;
                $notification->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    /**
     * Check for new notifications (count only)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNewNotifications()
    {
        $count = Notification::where('reciver_id', Auth::id())
            ->where('status', 0)
            ->count();
            
        return response()->json([
            'count' => $count
        ]);
    }
}
