<?php
// app/Http/Controllers/ChatAdminController.php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatAdminController extends Controller
{
    public function index()
    {
        // Récupérer tous les chats avec les derniers messages
        $chats = Chat::with(['user', 'messages'])
            ->where(function($query) {
                // Admin voit tous les chats
                if (auth()->user()->role === 'admin') {
                    return;
                }
                // Agent voit ses chats assignés
                $query->where('agent_id', auth()->id());
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.chat.index', compact('chats'));
    }

    /**
     * Get messages for a specific chat
     */
    public function getMessages($chatId)
    {
        $messages = Message::where('chat_id', $chatId)
            ->with('user', 'attachments', 'replyTo')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:5000',
            'reply_to_id' => 'nullable|exists:messages,id'
        ]);

        $message = Message::create([
            'chat_id' => $validated['chat_id'],
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'reply_to_id' => $validated['reply_to_id'] ?? null,
            'is_admin' => true,
        ]);

        // Broadcast via Ably (sera ajouté en Phase 3)
        // broadcast(new MessageSent($message))->toOthers();
        
        return response()->json($message->load('user', 'replyTo'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->update(['readed' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Upload file (Phase 4)
     */
    public function uploadFile(Request $request)
    {
        // TODO: Implement chunked file upload
        // Sera ajouté en Phase 4
    }
}