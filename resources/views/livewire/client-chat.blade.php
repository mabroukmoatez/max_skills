<div class="client-chat-widget" x-data="clientChatHandler(@entangle('chatId'))">
    <!-- Chat Toggle Button -->
    <button 
        class="chat-toggle-btn" 
        @click="toggleChat()"
        :class="{ 'has-unread': hasUnread }"
    >
        <i class="bi bi-chat-dots-fill"></i>
        <span class="chat-badge" x-show="hasUnread" x-text="unreadCount"></span>
    </button>

    <!-- Chat Window -->
    <div class="chat-widget-window" x-show="isOpen" x-cloak x-transition>
        <!-- Chat Header -->
        <div class="chat-widget-header">
            <div class="chat-widget-title">
                <i class="bi bi-headset"></i>
                <span>Support Chat</span>
            </div>
            <div class="chat-widget-actions">
                <button @click="minimizeChat()" class="widget-action-btn" title="Minimize">
                    <i class="bi bi-dash-lg"></i>
                </button>
                <button @click="closeChat()" class="widget-action-btn" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-widget-messages" id="client-chat-messages" wire:poll.5s="loadMessages">
            @if(count($messages) === 0)
                <div class="chat-welcome">
                    <div class="welcome-icon">
                        <i class="bi bi-chat-heart"></i>
                    </div>
                    <h4>Welcome to Support!</h4>
                    <p>Need help? Send us a message and we'll get back to you shortly.</p>
                </div>
            @else
                <div class="message-date-divider">
                    <span class="date-badge">Today</span>
                </div>

                @foreach($messages as $message)
                    <div 
                        wire:key="msg-{{ $message->id }}"
                        class="chat-message {{ $message->is_admin ? 'admin' : 'user' }}"
                        data-message-id="{{ $message->id }}"
                    >
                        <!-- Admin messages with avatar -->
                        @if($message->is_admin)
                            <img 
                                src="{{ $message->sender->path_photo ?? asset('assets/images/support-avatar.png') }}" 
                                class="message-avatar-small"
                                alt="Support"
                            >
                        @endif

                        <div class="message-bubble-small">
                            <!-- Reply Preview -->
                            @if($message->replyTo)
                                <div class="reply-preview-small">
                                    <div class="reply-indicator-small"></div>
                                    <p class="reply-text-small">{{ \Str::limit($message->replyTo->message, 40) }}</p>
                                </div>
                            @endif

                            <!-- Message Content -->
                            @if($message->type === 'text')
                                <p class="message-text-small">{{ $message->message }}</p>
                            
                            @elseif($message->type === 'image')
                                <div class="message-image-small">
                                    <img src="{{ Storage::url($message->file_path) }}" alt="Image">
                                </div>
                            
                            @else
                                <div class="message-file-small">
                                    <i class="bi {{ $message->file_icon }}"></i>
                                    <a href="{{ Storage::url($message->file_path) }}" target="_blank">
                                        {{ $message->file_name }}
                                    </a>
                                </div>
                            @endif

                            <!-- Message Time -->
                            <span class="message-time-small">{{ $message->sent_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" x-show="isTyping" x-cloak>
            <span></span><span></span><span></span>
        </div>

        <!-- Input Area -->
        <div class="chat-widget-input">
            <!-- Reply Preview -->
            @if($replyToMessage)
                <div class="reply-to-preview-small">
                    <div class="reply-preview-content-small">
                        <p class="reply-text-small">{{ \Str::limit($replyToMessage['message'], 40) }}</p>
                    </div>
                    <button wire:click="cancelReply" class="reply-cancel-btn-small">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            @endif

            <div class="input-wrapper-small">
                <button class="input-action-btn-small" title="Attach" onclick="document.getElementById('client-file-input').click()">
                    <i class="bi bi-paperclip"></i>
                </button>
                <input 
                    type="file" 
                    id="client-file-input" 
                    wire:model="files" 
                    multiple 
                    style="display: none;"
                >
                
                <input 
                    type="text" 
                    class="message-input-small" 
                    wire:model="newMessage" 
                    placeholder="Type your message..."
                    wire:keydown.enter.prevent="sendMessage"
                    @input="handleTyping()"
                >
                
                <button 
                    class="send-btn-small" 
                    wire:click="sendMessage"
                    @if(empty($newMessage) && empty($files)) disabled @endif
                >
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>

            <!-- File Preview -->
            @if(!empty($files))
                <div class="files-preview-small">
                    <i class="bi bi-paperclip"></i> 
                    {{ count($files) }} file(s) selected
                </div>
            @endif
        </div>

        <!-- Powered By -->
        <div class="chat-widget-footer">
            <span>Powered by Your Platform</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clientChatHandler', (chatId) => ({
        isOpen: false,
        isMinimized: false,
        hasUnread: false,
        unreadCount: 0,
        isTyping: false,
        typingTimeout: null,

        init() {
            // Check for unread messages
            this.checkUnread();

            // Listen for new messages
            this.$wire.on('messageReceived', (data) => {
                if (!this.isOpen) {
                    this.hasUnread = true;
                    this.unreadCount++;
                }
                this.scrollToBottom();
            });

            // Listen for sent messages
            this.$wire.on('messageSent', () => {
                this.scrollToBottom();
            });

            // Auto open chat if there's a query parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('openChat') === 'true') {
                this.isOpen = true;
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.hasUnread = false;
                this.unreadCount = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },

        closeChat() {
            this.isOpen = false;
        },

        minimizeChat() {
            this.isOpen = false;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('client-chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        checkUnread() {
            // This would normally check against your backend
            // For now, we'll use the wire model
        },

        handleTyping() {
            this.isTyping = true;
            
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.isTyping = false;
            }, 1000);
        }
    }));
});
</script>
@endpush
