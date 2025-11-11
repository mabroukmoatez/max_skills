<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Ably\AblyRest;
use Ably\Exceptions\AblyException;
use Illuminate\Support\Facades\Storage;

class ChatIndex extends Component
{
    use WithFileUploads;

    public $search = '';
    public $selectedChatId;
    public $newMessage = '';
    public $unreadMessagesCount = 0;
    public $activeTab = 'general';
    public $files = [];
    public $chatIdToDelete;

    protected $listeners = ['refreshChat' => 'render'];

    public function selectChat($chatId)
    {
        $this->selectedChatId = $chatId;
        $this->markAsRead($chatId);
        $chat = Chat::with(['messages'])->find($chatId);
        $lastMessageId = $chat ? $chat->messages->max('id') ?? 0 : 0;
        $this->dispatch('chatSelected', chatId: $chatId, lastMessageId: $lastMessageId);
    }

    public function sendMessageOrFiles($chatId)
    {
        $hasMessage = trim($this->newMessage) !== '';
        $hasFiles = !empty($this->files);

        if (!$hasMessage && !$hasFiles) {
            return;
        }

        // Handle message if present
        if ($hasMessage) {
            $message = Message::create([
                'chat_id' => $chatId,
                'sender_id' => Auth::id(),
                'message' => $this->newMessage,
                'page_type' => 'course',
                'page_id' => 1,
                'is_admin' => true,
                'readed' => false,
                'sent_at' => now(),
                'type' => 'message',
            ]);

            // Publish message to Ably
            try {
                $ably = new AblyRest(['key' => '_ZKbEA.e-I2tg:gB1Kcafu6TtDA8M8RpclXxeirMs8Ag43Dcmb8b-JQNs']);
                $channel = $ably->channels->get("chat:{$chatId}");
                $channel->publish('message', [
                    'id' => $message->id,
                    'chat_id' => (int) $chatId,
                    'message' => $message->message,
                    'is_admin' => true,
                    'sent_at' => $message->sent_at->toISOString(),
                    'sender_name' => Auth::user()->firstname . ' ' . Auth::user()->name,
                    'readed' => false,
                    'sender' => [
                        'id' => Auth::id(),
                        'name' => Auth::user()->name,
                        'firstname' => Auth::user()->firstname,
                        'path_photo' => Auth::user()->path_photo ?? 'assets/images/ai_avtar/2.jpg'
                    ]
                ]);
                \Log::info('Admin message published to Ably', ['message_id' => $message->id, 'chat_id' => $chatId]);
            } catch (AblyException $e) { 
                \Log::error('Failed to publish admin message to Ably', ['error' => $e->getMessage(), 'message_id' => $message->id]);
            } catch (\Exception $e) {
                \Log::error('Unexpected error publishing admin message to Ably', ['error' => $e->getMessage(), 'message_id' => $message->id]);
            }

            $this->newMessage = '';
            $this->dispatch('messageSent', messageId: $message->id);
        }

        // Handle files if present
        if ($hasFiles) {
            $this->validate([
                'files.*' => 'file|max:902400', // 10MB max per file
            ]);

            foreach ($this->files as $file) {
                $path = 'storage/'.$file->store('uploads', 'public');
                $filename = $file->getClientOriginalName();

                $message = Message::create([
                    'chat_id' => $chatId,
                    'sender_id' => Auth::id(),
                    'message' => $path,
                    'file_name' => $filename,
                    'page_type' => 'course',
                    'page_id' => 1,
                    'is_admin' => true,
                    'readed' => false,
                    'sent_at' => now(),
                    'type' => 'file',
                ]);

                // Publish file to Ably
                try {
                    $ably = new AblyRest(['key' => '_ZKbEA.e-I2tg:gB1Kcafu6TtDA8M8RpclXxeirMs8Ag43Dcmb8b-JQNs']);
                    $channel = $ably->channels->get("chat:{$chatId}");
                    $channel->publish('message', [
                        'id' => $message->id,
                        'chat_id' => (int) $chatId,
                        'message' => $path,
                        'file_name' => $filename,
                        'is_admin' => true,
                        'sent_at' => $message->sent_at->toISOString(),
                        'sender_name' => Auth::user()->firstname . ' ' . Auth::user()->name,
                        'readed' => false,
                        'type' => 'file',
                        'sender' => [
                            'id' => Auth::id(),
                            'name' => Auth::user()->name,
                            'firstname' => Auth::user()->firstname,
                            'path_photo' => Auth::user()->path_photo ?? 'assets/images/ai_avtar/2.jpg'
                        ]
                    ]);
                    \Log::info('Admin file message published to Ably', ['message_id' => $message->id, 'chat_id' => $chatId]);
                } catch (AblyException $e) {
                    \Log::error('Failed to publish admin file message to Ably', ['error' => $e->getMessage(), 'message_id' => $message->id]);
                } catch (\Exception $e) {
                    \Log::error('Unexpected error publishing admin file message to Ably', ['error' => $e->getMessage(), 'message_id' => $message->id]);
                }

                $this->dispatch('messageSent', messageId: $message->id);
            }

            $this->files = [];
            $this->dispatch('filesUploaded');
        }

        $this->dispatch('refreshChat');
    }

    public function markAsRead($chatId)
    {
        Message::where('chat_id', $chatId)
            ->where('is_admin', false)
            ->where('readed', false)
            ->update(['readed' => true]);

        $this->dispatch('refreshChat');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function deleteChat()
    {
        if (!$this->chatIdToDelete) {
            return ;
        }
        try {

            $chat = Chat::findOrFail($this->chatIdToDelete);

            $chat->messages()->delete();

            $chat->delete();


            if ($this->selectedChatId == $this->chatIdToDelete) {
                $this->selectedChatId = null;
            }

            $this->chatIdToDelete = null;

            $this->dispatch('chatDeleted');

        } catch (\Exception $e) {
            \Log::error('Error deleting chat: ' . $e->getMessage());
            $this->dispatch('error', message: 'An error occurred while deleting the chat.');
        }

    }
    public function render()
    {
        $query = Chat::with(['user', 'messages'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->whereRaw("CONCAT(firstname, ' ', name) LIKE ?", ['%' . $this->search . '%']);
                });
            });

        if ($this->activeTab === 'non-lus') {
            $query->whereHas('messages', function ($q) {
                $q->where('readed', false)->where('is_admin', false);
            });
        }

        if ($this->activeTab === 'ouvert') {
            $query->whereHas('messages', function ($q) {
                $q->where('sent_at', '>=', Carbon::now()->subHours(24));
            });
        }

        $chats = $query->get()
            ->sortByDesc(function ($chat) {
                return $chat->messages->max('sent_at');
            });

        $this->unreadMessagesCount = $chats->sum(function ($chat) {
            return $chat->messages->where('readed', false)->where('is_admin', false)->count();
        });

        // Select the last chat by default if no chat is selected
        if (!$this->selectedChatId && $chats->isNotEmpty()) {
            $lastChat = $chats->first();
            $this->selectedChatId = $lastChat->id;
            $this->markAsRead($lastChat->id);
            $lastMessageId = $lastChat->messages->max('id') ?? 0;
            $this->dispatch('chatSelected', chatId: $lastChat->id, lastMessageId: $lastMessageId);
        }

        $selectedChat = $this->selectedChatId ? Chat::with(['messages.sender'])->find($this->selectedChatId) : null;

        return view('livewire.chat-index', [
            'chats' => $chats,
            'selectedChat' => $selectedChat,
            'unreadMessagesCount' => $this->unreadMessagesCount,
        ]);
    }
}
?>