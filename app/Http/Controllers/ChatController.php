<?php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ably\AblyRest;

class ChatController extends Controller
{
    public function getChat(Request $request)
    {
        $user = Auth::user();
        $pageType = 'chat';
        $pageId = 1;

        $chat = Chat::firstOrCreate(
            [
                'user_id' => $user->id,
                'page_type' => $pageType,
                'course_id' =>  'chat' ? $pageId : null,
                'chapter_id' => $pageType === 'chat' ? null : $pageId,
            ]
        );

        // Fetch messages
        $messages = $chat->messages()->orderBy('sent_at', 'asc')->get();
        $lastMessageId = $messages->max('id') ?? 0;
        return response()->json([
            'chat_id' => $chat->id,
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message, // Use 'message' instead of 'content'
                    'is_admin' => $message->is_admin,
                    'sent_at' => $message->sent_at ? $message->sent_at->toDateTimeString() : $message->created_at->toDateTimeString(),
                    'sender_name' => $message->sender->name,
                    'readed' => (bool) $message->readed,
                ];
            }),
            'last_message_id' => $lastMessageId
        ]);
    }
    public function getAblyToken(Request $request)
    {
        $ably = new AblyRest(['key' => '_ZKbEA.e-I2tg:gB1Kcafu6TtDA8M8RpclXxeirMs8Ag43Dcmb8b-JQNs']);
        $token = $ably->auth->requestToken([
            'clientId' => (string) Auth::id(),
            'capability' => [
                'chat:*' => ['publish', 'subscribe', 'presence']
            ]
        ]);
        return response()->json(['token' => $token->token]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chat,id',
            'message' => 'required|string|max:1000',
            'page_type' => 'required|in:course,chapter,chat',
            'page_id' => 'required|integer',
        ]);

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'page_type' => 'chat',
            'page_id' => 1,
            'is_admin' => Auth::user()->role === 'admin',
            'readed' => false,
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Message sent']);
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

    public function markAsRead(Request $request)
    {
        $chatId = $request->input('chat_id');
        Message::where('chat_id', $chatId)
            ->where('is_admin', true)
            ->where('readed', false)
            ->update(['readed' => true]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}