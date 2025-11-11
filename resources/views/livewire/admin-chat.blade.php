<div class="chat-container-wrapper">
    <div class="chat-container" x-data="chatHandler(@entangle('selectedChatId'))">
        
        <!-- Left Sidebar - Chat List -->
        <div class="chat-sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <div class="sidebar-header-content">
                    <h2 class="sidebar-title">Chats</h2>
                    <div class="sidebar-actions">
                        <button class="header-icon-btn" title="New Chat">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="header-icon-btn" title="Search" wire:click="$set('search', '')">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Box -->
            <div class="search-container">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search conversations"
                        class="search-input"
                    >
                    @if($search)
                        <button wire:click="$set('search', '')" class="search-clear">
                            <i class="bi bi-x"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filters -->
            <div class="chat-filters">
                <button 
                    wire:click="setTab('all')" 
                    class="filter-btn {{ $activeTab === 'all' ? 'active' : '' }}">
                    <i class="bi bi-chat-dots"></i>
                    All Chats
                </button>
                <button 
                    wire:click="setTab('unread')" 
                    class="filter-btn {{ $activeTab === 'unread' ? 'active' : '' }}">
                    <i class="bi bi-circle-fill unread-dot"></i>
                    Unread
                    @if($unreadCount > 0)
                        <span class="filter-badge">{{ $unreadCount }}</span>
                    @endif
                </button>
                <button 
                    wire:click="setTab('open')" 
                    class="filter-btn {{ $activeTab === 'open' ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    Recent
                </button>
            </div>

            <!-- Chats List -->
            <div class="chats-list" wire:loading.class="loading">
                @forelse($chats as $chat)
                    <div 
                        wire:key="chat-{{ $chat->id }}"
                        class="chat-item {{ $selectedChatId == $chat->id ? 'active' : '' }}" 
                        wire:click="selectChat({{ $chat->id }})"
                    >
                        <div class="chat-avatar-container">
                            <img 
                                src="{{ $chat->user->path_photo ?? asset('assets/images/default-avatar.png') }}" 
                                alt="{{ $chat->user->firstname }} {{ $chat->user->name }}"
                                class="chat-avatar"
                            >
                            @if($chat->user->is_online ?? false)
                                <span class="online-indicator"></span>
                            @endif
                        </div>
                        
                        <div class="chat-info">
                            <div class="chat-header-row">
                                <h4 class="chat-name">
                                    {{ trim(($chat->user->firstname ?? '') . ' ' . $chat->user->name) }}
                                </h4>
                                <span class="chat-time">
                                    {{ $chat->last_message ? $chat->last_message->sent_at->format('H:i') : '' }}
                                </span>
                            </div>
                            
                            <div class="chat-preview-row">
                                <p class="chat-preview-text">
                                    {{ $chat->last_message_preview }}
                                </p>
                                
                                @php
                                    $unreadCount = $chat->unread_count;
                                @endphp
                                
                                @if($unreadCount > 0)
                                    <span class="unread-badge">{{ $unreadCount }}</span>
                                @elseif($chat->last_message && $chat->last_message->is_admin)
                                    <i class="bi bi-check-all message-status {{ $chat->last_message->readed ? 'read' : 'sent' }}"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-chat-dots empty-icon"></i>
                        <p class="empty-text">No conversations yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Window -->
        <div class="chat-window">
            @if($selectedChat)
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <img 
                            src="{{ $selectedChat->user->path_photo ?? asset('assets/images/default-avatar.png') }}" 
                            alt="{{ $selectedChat->user->firstname }}"
                            class="chat-header-avatar"
                        >
                        <div class="chat-header-details">
                            <h3 class="chat-header-name">
                                {{ trim(($selectedChat->user->firstname ?? '') . ' ' . $selectedChat->user->name) }}
                            </h3>
                            <p class="chat-header-status">
                                @if($selectedChat->user->is_online ?? false)
                                    <span class="status-online">● Online</span>
                                @else
                                    <span class="status-offline">Last seen recently</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="chat-header-actions">
                        <button class="header-action-btn" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="header-action-btn" wire:click="toggleInfoPanel" title="Info">
                            <i class="bi bi-info-circle"></i>
                        </button>
                        <div class="dropdown">
                            <button class="header-action-btn" data-bs-toggle="dropdown" title="More">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#deleteChatModal" 
                                       wire:click="$set('chatIdToDelete', {{ $selectedChat->id }})">
                                        <i class="bi bi-trash"></i> Delete Chat
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="messages-container" id="chat-messages" wire:poll.5s="loadMessages">
                    <div class="message-date-divider">
                        <span class="date-badge">Today</span>
                    </div>

                    @foreach($selectedChat->messages as $message)
                        <div 
                            wire:key="message-{{ $message->id }}"
                            class="message-wrapper {{ $message->is_admin ? 'sent' : 'received' }}"
                            data-message-id="{{ $message->id }}"
                        >
                            @if(!$message->is_admin)
                                <img 
                                    src="{{ $message->sender->path_photo ?? asset('assets/images/default-avatar.png') }}" 
                                    class="message-avatar"
                                    alt="{{ $message->sender->name }}"
                                >
                            @endif

                            <div class="message-bubble">
                                <!-- Reply Preview -->
                                @if($message->replyTo)
                                    <div class="message-reply-preview">
                                        <div class="reply-indicator"></div>
                                        <div class="reply-content">
                                            <p class="reply-sender">{{ $message->replyTo->sender->name }}</p>
                                            <p class="reply-text">{{ \Str::limit($message->replyTo->message, 50) }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Message Content -->
                                <div class="message-content">
                                    @if($message->type === 'text')
                                        <p class="message-text">{{ $message->message }}</p>
                                    
                                    @elseif($message->type === 'image')
                                        <div class="message-image">
                                            <img src="{{ Storage::url($message->file_path) }}" alt="Image" loading="lazy">
                                        </div>
                                        @if($message->message)
                                            <p class="message-text">{{ $message->message }}</p>
                                        @endif
                                    
                                    @elseif($message->type === 'video')
                                        <div class="message-video">
                                            <video controls>
                                                <source src="{{ Storage::url($message->file_path) }}" type="{{ $message->file_type }}">
                                            </video>
                                        </div>
                                    
                                    @else
                                        <div class="message-file">
                                            <div class="file-icon">
                                                <i class="bi {{ $message->file_icon }}"></i>
                                            </div>
                                            <div class="file-info">
                                                <a href="{{ Storage::url($message->file_path) }}" 
                                                   target="_blank" 
                                                   class="file-name">
                                                    {{ $message->file_name }}
                                                </a>
                                                <span class="file-size">{{ $message->formatted_file_size }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Message Meta -->
                                    <div class="message-meta">
                                        <span class="message-time">{{ $message->sent_at->format('H:i') }}</span>
                                        @if($message->is_admin)
                                            <i class="bi bi-check-all message-check {{ $message->readed ? 'read' : 'sent' }}"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Actions -->
                                <div class="message-actions">
                                    <button wire:click="setReplyTo({{ $message->id }})" title="Reply">
                                        <i class="bi bi-reply"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div wire:loading wire:target="loadMessages" class="loading-indicator">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="input-area">
                    <!-- Reply Preview -->
                    @if($replyToMessage)
                        <div class="reply-to-preview">
                            <div class="reply-indicator-vertical"></div>
                            <div class="reply-preview-content">
                                <p class="reply-preview-sender">{{ $replyToMessage['sender_name'] }}</p>
                                <p class="reply-preview-text">{{ \Str::limit($replyToMessage['message'], 60) }}</p>
                            </div>
                            <button wire:click="cancelReply" class="reply-cancel-btn">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    @endif

                    <div class="input-wrapper">
                        <div class="input-actions-left">
                            <button class="input-action-btn" title="Emoji">
                                <i class="bi bi-emoji-smile"></i>
                            </button>
                            <button class="input-action-btn" title="Attach" onclick="document.getElementById('file-input-{{ $selectedChat->id }}').click()">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <input 
                                type="file" 
                                id="file-input-{{ $selectedChat->id }}" 
                                wire:model="files" 
                                multiple 
                                style="display: none;"
                            >
                        </div>
                        
                        <textarea 
                            class="message-input" 
                            wire:model="newMessage" 
                            placeholder="Type a message..."
                            rows="1"
                            wire:keydown.enter.prevent="sendMessage"
                            x-data="{ resize: function() { $el.style.height = '42px'; $el.style.height = $el.scrollHeight + 'px'; } }"
                            x-init="$watch('$wire.newMessage', () => resize())"
                            @input="resize()"
                        ></textarea>
                        
                        <button 
                            class="send-btn" 
                            wire:click="sendMessage"
                            @if(empty($newMessage) && empty($files)) disabled @endif
                        >
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    
                    <!-- File Preview -->
                    @if(!empty($files))
                        <div class="files-preview">
                            <i class="bi bi-paperclip"></i> 
                            <span>{{ count($files) }} file(s) selected</span>
                        </div>
                    @endif

                    <div wire:loading wire:target="sendMessage,files" class="sending-indicator">
                        <span class="spinner-border spinner-border-sm"></span> Sending...
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-chat-state">
                    <div class="empty-chat-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3 class="empty-chat-title">Select a conversation</h3>
                    <p class="empty-chat-text">Choose a conversation from the left to start messaging</p>
                </div>
            @endif
        </div>

        <!-- Right Sidebar - Info Panel -->
        @if($selectedChat && $showInfoPanel)
            <div class="info-panel" wire:transition>
                <div class="info-header">
                    <h3 class="info-title">Contact Info</h3>
                    <button wire:click="toggleInfoPanel" class="info-close-btn">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <!-- User Profile -->
                <div class="info-profile">
                    <img 
                        src="{{ $selectedChat->user->path_photo ?? asset('assets/images/default-avatar.png') }}" 
                        class="info-avatar"
                        alt="{{ $selectedChat->user->firstname }}"
                    >
                    <h4 class="info-name">{{ trim(($selectedChat->user->firstname ?? '') . ' ' . $selectedChat->user->name) }}</h4>
                    <p class="info-email">{{ $selectedChat->user->email }}</p>
                </div>

                <!-- Info Section: Created -->
                <div class="info-section">
                    <div class="info-section-header">
                        <i class="bi bi-calendar-event"></i>
                        <h5>Created</h5>
                    </div>
                    <div class="info-section-content">
                        {{ $selectedChat->created_at->format('d-m-Y H:i') }}
                    </div>
                </div>

                <!-- Info Section: Media -->
                @if($mediaMessages->count() > 0)
                    <div class="info-section">
                        <div class="info-section-header">
                            <i class="bi bi-image"></i>
                            <h5>Media</h5>
                            <span class="info-badge">{{ $mediaMessages->count() }}</span>
                        </div>
                        <div class="media-grid">
                            @foreach($mediaMessages->take(6) as $media)
                                <div class="media-item">
                                    @if($media->type === 'image')
                                        <img src="{{ Storage::url($media->file_path) }}" alt="Media">
                                    @else
                                        <div class="video-thumbnail">
                                            <i class="bi bi-play-circle"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($mediaMessages->count() > 6)
                            <div class="view-all-link">
                                View All <i class="bi bi-chevron-right"></i>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Info Section: Files -->
                @if($documentMessages->count() > 0)
                    <div class="info-section">
                        <div class="info-section-header">
                            <i class="bi bi-file-earmark"></i>
                            <h5>Files</h5>
                            <span class="info-badge">{{ $documentMessages->count() }}</span>
                        </div>
                        <div class="files-list">
                            @foreach($documentMessages->take(3) as $doc)
                                <div class="file-item">
                                    <div class="file-icon-box">
                                        <i class="bi {{ $doc->file_icon }}"></i>
                                    </div>
                                    <div class="file-details">
                                        <p class="file-name">{{ $doc->file_name }}</p>
                                        <p class="file-meta">
                                            {{ $doc->formatted_file_size }} • {{ $doc->sent_at->format('d-m-Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($documentMessages->count() > 3)
                            <div class="view-all-link">
                                View All <i class="bi bi-chevron-right"></i>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Delete Chat Modal -->
    <div class="modal fade" id="deleteChatModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this conversation? This action cannot be undone and all messages will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteChat" data-bs-dismiss="modal">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatHandler', (chatId) => ({
        init() {
            // Auto-scroll to bottom on load
            this.$nextTick(() => {
                this.scrollToBottom();
            });

            // Listen for new messages
            this.$wire.on('messageSent', () => {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            });
        },

        scrollToBottom() {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }));
});
</script>
@endpush
