/**
 * Real-Time Chat with Ably
 * Handles all real-time messaging functionality
 */

class AblyChat {
    constructor(apiKey, chatId) {
        this.apiKey = apiKey;
        this.chatId = chatId;
        this.ably = null;
        this.channel = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        
        this.init();
    }

    /**
     * Initialize Ably connection
     */
    init() {
        try {
            // Initialize Ably with token auth
            this.ably = new Ably.Realtime({
                authUrl: '/ably/token',
                authMethod: 'GET',
                autoConnect: true,
            });

            this.setupConnectionHandlers();
            this.subscribeToChannel();
        } catch (error) {
            console.error('Failed to initialize Ably:', error);
            this.handleError(error);
        }
    }

    /**
     * Setup connection event handlers
     */
    setupConnectionHandlers() {
        this.ably.connection.on('connected', () => {
            console.log('✅ Connected to Ably');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.showConnectionStatus('connected');
        });

        this.ably.connection.on('disconnected', () => {
            console.log('❌ Disconnected from Ably');
            this.isConnected = false;
            this.showConnectionStatus('disconnected');
        });

        this.ably.connection.on('suspended', () => {
            console.log('⚠️ Connection suspended');
            this.isConnected = false;
            this.showConnectionStatus('suspended');
        });

        this.ably.connection.on('failed', (error) => {
            console.error('❌ Connection failed:', error);
            this.isConnected = false;
            this.showConnectionStatus('failed');
            this.handleConnectionFailure();
        });

        this.ably.connection.on('closed', () => {
            console.log('Connection closed');
            this.isConnected = false;
        });
    }

    /**
     * Subscribe to chat channel
     */
    subscribeToChannel() {
        if (!this.chatId) {
            console.error('No chat ID provided');
            return;
        }

        const channelName = `chat:${this.chatId}`;
        this.channel = this.ably.channels.get(channelName);

        console.log(`📡 Subscribing to channel: ${channelName}`);

        // Subscribe to new messages
        this.channel.subscribe('new-message', (message) => {
            this.handleNewMessage(message.data);
        });

        // Subscribe to message read events
        this.channel.subscribe('message-read', (message) => {
            this.handleMessageRead(message.data);
        });

        // Subscribe to typing indicators
        this.channel.subscribe('typing', (message) => {
            this.handleTypingIndicator(message.data);
        });

        // Enter presence
        this.channel.presence.enter({ status: 'online' });

        // Subscribe to presence events
        this.channel.presence.subscribe('enter', (member) => {
            console.log(`User ${member.clientId} entered`);
            this.handlePresenceUpdate(member, 'enter');
        });

        this.channel.presence.subscribe('leave', (member) => {
            console.log(`User ${member.clientId} left`);
            this.handlePresenceUpdate(member, 'leave');
        });
    }

    /**
     * Handle incoming new message
     */
    handleNewMessage(data) {
        console.log('📨 New message received:', data);

        // Dispatch Livewire event
        if (window.Livewire) {
            window.Livewire.dispatch('messageReceived', data);
        }

        // Add message to DOM
        this.appendMessageToDOM(data);

        // Play notification sound
        this.playNotificationSound();

        // Show browser notification if tab is not active
        if (document.hidden) {
            this.showBrowserNotification(data);
        }

        // Scroll to bottom
        this.scrollToBottom();
    }

    /**
     * Handle message read event
     */
    handleMessageRead(data) {
        console.log('✓ Message read:', data);
        
        // Update message read status in DOM
        const messageElements = document.querySelectorAll(`[data-message-id="${data.messageId}"]`);
        messageElements.forEach(el => {
            const checkIcon = el.querySelector('.message-check');
            if (checkIcon) {
                checkIcon.classList.add('read');
            }
        });
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        const { userId, isTyping } = data;
        
        if (isTyping) {
            this.showTypingIndicator(userId);
        } else {
            this.hideTypingIndicator(userId);
        }
    }

    /**
     * Handle presence update
     */
    handlePresenceUpdate(member, action) {
        const userId = member.clientId;
        
        // Update online status in DOM
        const userElements = document.querySelectorAll(`[data-user-id="${userId}"]`);
        userElements.forEach(el => {
            const onlineIndicator = el.querySelector('.online-indicator');
            if (onlineIndicator) {
                if (action === 'enter') {
                    onlineIndicator.style.display = 'block';
                } else {
                    onlineIndicator.style.display = 'none';
                }
            }
        });

        // Update status text
        const statusElements = document.querySelectorAll(`[data-status-user="${userId}"]`);
        statusElements.forEach(el => {
            if (action === 'enter') {
                el.textContent = 'Online';
                el.classList.add('status-online');
                el.classList.remove('status-offline');
            } else {
                el.textContent = 'Offline';
                el.classList.add('status-offline');
                el.classList.remove('status-online');
            }
        });
    }

    /**
     * Publish typing indicator
     */
    publishTyping(isTyping) {
        if (!this.channel || !this.isConnected) return;

        this.channel.publish('typing', {
            userId: this.getCurrentUserId(),
            isTyping: isTyping
        });
    }

    /**
     * Publish message read event
     */
    publishMessageRead(messageId) {
        if (!this.channel || !this.isConnected) return;

        this.channel.publish('message-read', {
            messageId: messageId,
            userId: this.getCurrentUserId()
        });
    }

    /**
     * Append message to DOM
     */
    appendMessageToDOM(messageData) {
        const container = document.getElementById('chat-messages');
        if (!container) return;

        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-wrapper ${messageData.is_admin ? 'sent' : 'received'}`;
        messageDiv.setAttribute('data-message-id', messageData.id);

        // Build message HTML
        let messageHTML = '';

        if (!messageData.is_admin && messageData.sender) {
            messageHTML += `
                <img src="${messageData.sender.path_photo || '/assets/images/default-avatar.png'}" 
                     class="message-avatar" 
                     alt="${messageData.sender.full_name}">
            `;
        }

        messageHTML += `
            <div class="message-bubble">
                <div class="message-content">
        `;

        // Add message content based on type
        if (messageData.type === 'text') {
            messageHTML += `<p class="message-text">${this.escapeHtml(messageData.message)}</p>`;
        } else if (messageData.type === 'image') {
            messageHTML += `
                <div class="message-image">
                    <img src="${messageData.file_url}" alt="Image" loading="lazy">
                </div>
            `;
        } else {
            messageHTML += `
                <div class="message-file">
                    <div class="file-icon">
                        <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="file-info">
                        <a href="${messageData.file_url}" target="_blank" class="file-name">
                            ${this.escapeHtml(messageData.file_name)}
                        </a>
                    </div>
                </div>
            `;
        }

        // Add message meta
        const time = new Date(messageData.sent_at).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageHTML += `
                    <div class="message-meta">
                        <span class="message-time">${time}</span>
                        ${messageData.is_admin ? `
                            <i class="bi bi-check-all message-check ${messageData.readed ? 'read' : 'sent'}"></i>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        messageDiv.innerHTML = messageHTML;

        // Append to container
        container.appendChild(messageDiv);
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator(userId) {
        const container = document.getElementById('chat-messages');
        if (!container) return;

        // Remove existing typing indicator
        const existing = container.querySelector('.typing-indicator');
        if (existing) existing.remove();

        // Create typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator';
        typingDiv.innerHTML = `
            <span></span><span></span><span></span>
        `;

        container.appendChild(typingDiv);
        this.scrollToBottom();
    }

    /**
     * Hide typing indicator
     */
    hideTypingIndicator(userId) {
        const indicator = document.querySelector('.typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    /**
     * Show connection status
     */
    showConnectionStatus(status) {
        // You can implement a visual indicator here
        const statusMap = {
            'connected': { icon: '✅', text: 'Connected', color: '#00A884' },
            'disconnected': { icon: '❌', text: 'Disconnected', color: '#F44336' },
            'suspended': { icon: '⚠️', text: 'Connection Issues', color: '#FF9800' },
            'failed': { icon: '❌', text: 'Connection Failed', color: '#F44336' }
        };

        const statusInfo = statusMap[status] || statusMap['disconnected'];
        console.log(`${statusInfo.icon} ${statusInfo.text}`);

        // Dispatch event for UI to handle
        window.dispatchEvent(new CustomEvent('chat-connection-status', {
            detail: statusInfo
        }));
    }

    /**
     * Handle connection failure
     */
    handleConnectionFailure() {
        this.reconnectAttempts++;

        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.ably.connect();
            }, 2000 * this.reconnectAttempts);
        } else {
            console.error('Max reconnection attempts reached');
            this.showError('Unable to connect to chat server. Please refresh the page.');
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/assets/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Could not play sound:', e));
        } catch (error) {
            console.log('Notification sound not available');
        }
    }

    /**
     * Show browser notification
     */
    async showBrowserNotification(messageData) {
        if (!('Notification' in window)) return;

        if (Notification.permission === 'granted') {
            new Notification('New Message', {
                body: messageData.message || 'You have a new message',
                icon: messageData.sender?.path_photo || '/assets/images/default-avatar.png',
                tag: `chat-${this.chatId}`,
            });
        } else if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                this.showBrowserNotification(messageData);
            }
        }
    }

    /**
     * Scroll to bottom of messages
     */
    scrollToBottom() {
        setTimeout(() => {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    }

    /**
     * Get current user ID
     */
    getCurrentUserId() {
        // You should implement this based on your auth system
        return document.querySelector('[data-current-user-id]')?.dataset.currentUserId || null;
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show error message
     */
    showError(message) {
        console.error('Chat Error:', message);
        // Implement your own error display logic
    }

    /**
     * Handle errors
     */
    handleError(error) {
        console.error('Ably Error:', error);
        this.showError('An error occurred with the chat connection.');
    }

    /**
     * Change chat channel
     */
    changeChannel(newChatId) {
        // Unsubscribe from old channel
        if (this.channel) {
            this.channel.presence.leave();
            this.channel.unsubscribe();
        }

        // Update chat ID and subscribe to new channel
        this.chatId = newChatId;
        this.subscribeToChannel();
    }

    /**
     * Disconnect and cleanup
     */
    disconnect() {
        if (this.channel) {
            this.channel.presence.leave();
            this.channel.unsubscribe();
        }

        if (this.ably) {
            this.ably.close();
        }

        this.isConnected = false;
        console.log('Disconnected from chat');
    }
}

// Initialize chat when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Livewire to be ready
    document.addEventListener('livewire:init', () => {
        let chatInstance = null;

        // Listen for chat selected event
        Livewire.on('chatSelected', (data) => {
            const { chatId, lastMessageId } = data[0] || data;
            
            if (chatInstance) {
                chatInstance.changeChannel(chatId);
            } else {
                const apiKey = document.querySelector('meta[name="ably-api-key"]')?.content;
                if (apiKey) {
                    chatInstance = new AblyChat(apiKey, chatId);
                } else {
                    console.error('Ably API key not found');
                }
            }
        });

        // Listen for chat ready event (for client chat widget)
        Livewire.on('chatReady', (data) => {
            const { chatId, lastMessageId } = data[0] || data;
            
            if (!chatInstance) {
                const apiKey = document.querySelector('meta[name="ably-api-key"]')?.content;
                if (apiKey) {
                    chatInstance = new AblyChat(apiKey, chatId);
                }
            }
        });

        // Setup typing indicator
        let typingTimeout;
        const messageInputs = document.querySelectorAll('.message-input, .message-input-small');
        
        messageInputs.forEach(input => {
            input.addEventListener('input', () => {
                if (chatInstance && chatInstance.isConnected) {
                    chatInstance.publishTyping(true);
                    
                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        chatInstance.publishTyping(false);
                    }, 1000);
                }
            });
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (chatInstance) {
                chatInstance.disconnect();
            }
        });
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AblyChat;
}
