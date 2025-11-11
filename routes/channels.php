<?php
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::findOrFail($chatId);
    return Auth::check() && ($chat->user_id === $user->id || $user->is_admin);
});