<?php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with(['user', 'messages'])
            ->get()
            ->groupBy('user_id');

        return view('admin.chats.index', compact('chats'));
    }

    public function show(Chat $chat)
    {
        $messages = $chat->messages()->orderBy('sent_at', 'asc')->get();
        // Mark client messages as read
        $chat->messages()->where('is_admin', false)->where('readed', false)->update(['readed' => true]);

        return view('admin.chats.show', compact('chat', 'messages'));
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'page_type' => 'course',
            'page_id' => 1,
            'is_admin' => true,
            'readed' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'Message sent',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_admin' => $message->is_admin,
                'sent_at' => $message->sent_at->toDateTimeString(),
                'sender_name' => $message->sender->name,
                'readed' => $message->readed,
            ],
        ]);
    }

    public function pollMessages(Request $request, Chat $chat)
    {
        $lastMessageId = $request->input('last_message_id', 0);
    
        $messages = $chat->messages()
            ->where('id', '>', $lastMessageId)
            ->orderBy('sent_at', 'asc')
            ->get();
    
        // Mark new client messages as read
        $chat->messages()
            ->where('is_admin', false)
            ->where('readed', false)
            ->whereIn('id', $messages->pluck('id'))
            ->update(['readed' => true]);
    
        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_admin' => $message->is_admin,
                    'sent_at' => $message->sent_at->toDateTimeString(),
                    'sender_name' => $message->sender->name,
                    'readed' => $message->readed,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'firstname' => $message->sender->firstname,
                        'path_photo' => $message->sender->path_photo,
                    ],
                ];
            })->toArray(),
        ]);
    }

    public function markAsRead(Chat $chat)
    {
        $chat->messages()
            ->where('is_admin', false)
            ->where('readed', false)
            ->update(['readed' => true]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}