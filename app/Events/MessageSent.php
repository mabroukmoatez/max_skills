<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->chat_id),
        ];
    }

    public function broadcastWith(): array
    {
        $attachmentUrls = $this->message->messageAttachments->pluck('file_path')->map(function ($path) {
            return url('storage/' . $path);
        })->toArray();

        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'is_admin' => $this->message->is_admin,
            'sent_at' => $this->message->sent_at->toDateTimeString(),
            'readed' => $this->message->readed,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'firstname' => $this->message->sender->firstname,
                'path_photo' => $this->message->sender->path_photo,
            ],
            'attachment_urls' => $attachmentUrls,
        ];
    }
}