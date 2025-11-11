
class ChatRealtime {
    constructor(chatId, userId, isAdmin = false) {
        this.chatId = chatId;
        this.userId = userId;
        this.isAdmin = isAdmin;
        this.ably = null;
        this.channel = null;
        this.lastMessageId = 0;
        this.isLoadingMore = false;
        this.hasMoreMessages = true;
        this.replyTo = null;
        
        this.init();
    }

    async init() {
        await this.initializeAbly();
        this.setupEventListeners();
        this.setupIntersectionObserver();
        this.scrollToBottom();
    }

    /**
     * Initialize Ably Real-Time Connection
     */
    async initializeAbly() {
        try {
            // Get auth token from server
            const response = await fetch('/ably/token');
            const data = await response.json();
            
            if (!data.success) {
                console.error('Failed to get Ably token');
                this.fallbackToPolling();
                return;
            }

            // Initialize Ably
            this.ably = new Ably.Realtime({
                token: data.token,
                clientId: this.userId.toString()
            });

            // Subscribe to chat channel
            this.channel = this.ably.channels.get(`chat:${this.chatId}`);
            
            // Listen for new messages
            this.channel.subscribe('message', (message) => {
                this.handleNewMessage(message.data);
            });

            // Listen for presence (online/offline status)
            this.channel.presence.subscribe('enter', (member) => {
                this.updateUserStatus(member.clientId, true);
            });

            this.channel.presence.subscribe('leave', (member) => {
                this.updateUserStatus(member.clientId, false);
            });

            // Enter presence
            this.channel.presence.enter();

            console.log('Ably connected successfully');
        } catch (error) {
            console.error('Ably initialization failed:', error);
            this.fallbackToPolling();
        }
    }

    /**
     * Fallback to polling if Ably fails
     */
    fallbackToPolling() {
        setInterval(() => this.pollNewMessages(), 3000);
    }

    /**
     * Poll for new messages (fallback)
     */
    async pollNewMessages() {
        try {
            const url = this.isAdmin 
                ? `/admin/chats/${this.chatId}/poll?last_message_id=${this.lastMessageId}`
                : `/chat/poll?chat_id=${this.chatId}&last_message_id=${this.lastMessageId}`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => this.appendMessage(msg));
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Polling failed:', error);
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Send message on Enter key
        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Auto-resize textarea
            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = messageInput.scrollHeight + 'px';
            });
        }

        // Send button
        const sendBtn = document.getElementById('send-btn');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        // File upload
        const fileInput = document.getElementById('file-input');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleFileSelect(e.target.files);
            });
        }

        // Attach file button
        const attachBtn = document.getElementById('attach-btn');
        if (attachBtn) {
            attachBtn.addEventListener('click', () => {
                fileInput?.click();
            });
        }

        // Image click for fullscreen
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('message-image')) {
                this.showImageFullscreen(e.target.src);
            }
        });

        // Message context menu (right-click to reply)
        document.addEventListener('contextmenu', (e) => {
            const messageBubble = e.target.closest('.message-bubble');
            if (messageBubble) {
                e.preventDefault();
                this.showContextMenu(e, messageBubble);
            }
        });

        // Cancel reply
        const cancelReplyBtn = document.getElementById('cancel-reply');
        if (cancelReplyBtn) {
            cancelReplyBtn.addEventListener('click', () => this.cancelReply());
        }
    }

    /**
     * Setup Intersection Observer for lazy loading
     */
    setupIntersectionObserver() {
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;

        const sentinel = document.createElement('div');
        sentinel.id = 'load-more-sentinel';
        sentinel.style.height = '1px';
        messagesContainer.insertBefore(sentinel, messagesContainer.firstChild);

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.isLoadingMore && this.hasMoreMessages) {
                this.loadMoreMessages();
            }
        }, {
            root: messagesContainer,
            threshold: 0.1
        });

        observer.observe(sentinel);
    }

    /**
     * Load more messages (lazy loading)
     */
    async loadMoreMessages() {
        if (this.isLoadingMore || !this.hasMoreMessages) return;

        this.isLoadingMore = true;
        this.showLoadingIndicator();

        try {
            const firstMessage = document.querySelector('.message');
            const beforeId = firstMessage?.dataset.messageId || null;

            const url = this.isAdmin
                ? `/admin/chats/${this.chatId}/messages?before_id=${beforeId}&per_page=50`
                : `/chat/messages?chat_id=${this.chatId}&before_id=${beforeId}&per_page=50`;

            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                const messagesContainer = document.getElementById('messages-container');
                const scrollHeight = messagesContainer.scrollHeight;

                data.messages.forEach(msg => this.prependMessage(msg));

                this.hasMoreMessages = data.has_more;

                // Maintain scroll position
                const newScrollHeight = messagesContainer.scrollHeight;
                messagesContainer.scrollTop = newScrollHeight - scrollHeight;
            }
        } catch (error) {
            console.error('Failed to load more messages:', error);
        } finally {
            this.isLoadingMore = false;
            this.hideLoadingIndicator();
        }
    }

    /**
     * Send message
     */
    async sendMessage() {
        const messageInput = document.getElementById('message-input');
        const message = messageInput?.value.trim();

        if (!message && !this.replyTo) return;

        const url = this.isAdmin
            ? `/admin/chats/${this.chatId}/send`
            : '/chat/send';

        const formData = new FormData();
        if (this.isAdmin) {
            formData.append('message', message);
        } else {
            formData.append('chat_id', this.chatId);
            formData.append('message', message);
            formData.append('page_type', 'chat');
            formData.append('page_id', '1');
        }

        if (this.replyTo) {
            formData.append('reply_to_id', this.replyTo.id);
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                this.cancelReply();
                
                // Message will be added via Ably
            } else {
                alert('Failed to send message');
            }
        } catch (error) {
            console.error('Send message error:', error);
            alert('Failed to send message');
        }
    }

    /**
     * Handle file selection
     */
    async handleFileSelect(files) {
        if (!files || files.length === 0) return;

        for (let i = 0; i < files.length; i++) {
            await this.uploadFile(files[i]);
        }
    }

    /**
     * Upload file with progress tracking
     */
    async uploadFile(file) {
        const uploadId = Date.now() + Math.random();
        this.showUploadProgress(uploadId, file.name, 0);

        const url = this.isAdmin
            ? `/admin/chats/${this.chatId}/upload`
            : '/chat/upload';

        const formData = new FormData();
        formData.append('file', file);
        
        if (!this.isAdmin) {
            formData.append('chat_id', this.chatId);
        }

        if (this.replyTo) {
            formData.append('reply_to_id', this.replyTo.id);
        }

        try {
            const xhr = new XMLHttpRequest();

            // Progress tracking
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentage = Math.round((e.loaded / e.total) * 100);
                    this.updateUploadProgress(uploadId, percentage);
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        this.hideUploadProgress(uploadId);
                        this.cancelReply();
                        // Message will be added via Ably
                    } else {
                        alert('Upload failed');
                        this.hideUploadProgress(uploadId);
                    }
                } else {
                    alert('Upload failed');
                    this.hideUploadProgress(uploadId);
                }
            });

            xhr.addEventListener('error', () => {
                alert('Upload failed');
                this.hideUploadProgress(uploadId);
            });

            xhr.open('POST', url);
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content);
            xhr.send(formData);

        } catch (error) {
            console.error('Upload error:', error);
            this.hideUploadProgress(uploadId);
        }
    }

    /**
     * Show upload progress
     */
    showUploadProgress(uploadId, filename, percentage) {
        let progressContainer = document.getElementById('upload-progress-container');
        
        if (!progressContainer) {
            progressContainer = document.createElement('div');
            progressContainer.id = 'upload-progress-container';
            progressContainer.className = 'upload-progress';
            document.querySelector('.input-area').prepend(progressContainer);
        }

        const progressItem = document.createElement('div');
        progressItem.id = `upload-${uploadId}`;
        progressItem.className = 'upload-item';
        progressItem.innerHTML = `
            <i class="bi bi-file-earmark upload-icon"></i>
            <div class="upload-details">
                <div class="upload-filename">${filename}</div>
                <div class="upload-progress-bar">
                    <div class="upload-progress-fill" style="width: ${percentage}%"></div>
                </div>
            </div>
            <span class="upload-percentage">${percentage}%</span>
        `;

        progressContainer.appendChild(progressItem);
    }

    /**
     * Update upload progress
     */
    updateUploadProgress(uploadId, percentage) {
        const progressItem = document.getElementById(`upload-${uploadId}`);
        if (progressItem) {
            const progressFill = progressItem.querySelector('.upload-progress-fill');
            const percentageSpan = progressItem.querySelector('.upload-percentage');
            
            if (progressFill) progressFill.style.width = `${percentage}%`;
            if (percentageSpan) percentageSpan.textContent = `${percentage}%`;
        }
    }

    /**
     * Hide upload progress
     */
    hideUploadProgress(uploadId) {
        setTimeout(() => {
            const progressItem = document.getElementById(`upload-${uploadId}`);
            if (progressItem) {
                progressItem.remove();
            }

            const progressContainer = document.getElementById('upload-progress-container');
            if (progressContainer && progressContainer.children.length === 0) {
                progressContainer.remove();
            }
        }, 1000);
    }

    /**
     * Handle incoming message from Ably
     */
    handleNewMessage(message) {
        // Don't add if it's our own message (already added optimistically)
        if (message.sender.id == this.userId) return;

        this.appendMessage(message);
        this.scrollToBottom();
    }

    /**
     * Append message to chat
     */
    appendMessage(message) {
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;

        // Check if message already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const messageElement = this.createMessageElement(message);
        messagesContainer.appendChild(messageElement);

        // Update last message ID
        if (message.id > this.lastMessageId) {
            this.lastMessageId = message.id;
        }
    }

    /**
     * Prepend message (for lazy loading)
     */
    prependMessage(message) {
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;

        // Check if message already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const sentinel = document.getElementById('load-more-sentinel');
        const messageElement = this.createMessageElement(message);
        
        if (sentinel && sentinel.nextSibling) {
            messagesContainer.insertBefore(messageElement, sentinel.nextSibling);
        } else {
            messagesContainer.appendChild(messageElement);
        }
    }

    /**
     * Create message HTML element
     */
    createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `message ${message.is_admin ? 'sent' : 'received'}`;
        div.dataset.messageId = message.id;

        let content = '';

        // Reply preview
        if (message.reply_to) {
            content += `
                <div class="reply-preview">
                    <div class="reply-sender">${message.reply_to.sender_name}</div>
                    <div class="reply-text">${this.escapeHtml(message.reply_to.message || 'File')}</div>
                </div>
            `;
        }

        // Message content based on type
        if (message.type === 'image') {
            content += `
                <div class="image-message">
                    <img src="/${message.message}" alt="${message.file_name}" class="message-image">
                </div>
            `;
        } else if (message.type === 'document') {
            content += `
                <div class="document-message" onclick="window.open('/${message.message}', '_blank')">
                    <i class="${message.file_icon} document-icon"></i>
                    <div class="document-info">
                        <div class="document-name">${message.file_name}</div>
                        <div class="document-size">${message.file_size_formatted}</div>
                    </div>
                </div>
            `;
        } else {
            content += `<p class="message-text">${this.escapeHtml(message.message)}</p>`;
        }

        // Message meta
        content += `
            <div class="message-meta">
                <span>${message.sent_at_formatted}</span>
                ${message.is_admin ? `<i class="bi bi-check-all ${message.readed ? 'read' : ''}"></i>` : ''}
            </div>
        `;

        div.innerHTML = `<div class="message-bubble">${content}</div>`;

        return div;
    }

    /**
     * Show context menu for reply
     */
    showContextMenu(event, messageBubble) {
        // Remove existing context menu
        const existingMenu = document.querySelector('.message-context-menu');
        if (existingMenu) existingMenu.remove();

        const messageElement = messageBubble.closest('.message');
        const messageId = messageElement.dataset.messageId;
        const messageText = messageBubble.querySelector('.message-text')?.textContent || 'File';

        const menu = document.createElement('div');
        menu.className = 'message-context-menu';
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';
        menu.innerHTML = `
            <div class="message-context-menu-item" onclick="chatRealtime.replyToMessage('${messageId}', '${this.escapeHtml(messageText).replace(/'/g, "\\'")}')">
                <i class="bi bi-reply"></i>
                Reply
            </div>
        `;

        document.body.appendChild(menu);

        // Close menu on click outside
        setTimeout(() => {
            document.addEventListener('click', function closeMenu() {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            });
        }, 0);
    }

    /**
     * Reply to message
     */
    replyToMessage(messageId, messageText) {
        this.replyTo = { id: messageId, text: messageText };

        // Show reply bar
        let replyBar = document.getElementById('reply-bar');
        if (!replyBar) {
            replyBar = document.createElement('div');
            replyBar.id = 'reply-bar';
            replyBar.className = 'reply-bar';
            document.querySelector('.input-wrapper').before(replyBar);
        }

        replyBar.innerHTML = `
            <div class="reply-content">
                <div class="reply-to-label">Replying to:</div>
                <div class="reply-message-preview">${messageText}</div>
            </div>
            <button class="cancel-reply" id="cancel-reply">
                <i class="bi bi-x"></i>
            </button>
        `;

        document.getElementById('cancel-reply').addEventListener('click', () => this.cancelReply());
        document.getElementById('message-input')?.focus();
    }

    /**
     * Cancel reply
     */
    cancelReply() {
        this.replyTo = null;
        const replyBar = document.getElementById('reply-bar');
        if (replyBar) replyBar.remove();
    }

    /**
     * Show image in fullscreen
     */
    showImageFullscreen(imageSrc) {
        let modal = document.getElementById('image-modal');
        
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'image-modal';
            modal.className = 'image-modal';
            modal.innerHTML = `
                <div class="image-modal-content">
                    <span class="image-modal-close">&times;</span>
                    <img src="" alt="Full size image">
                </div>
            `;
            document.body.appendChild(modal);

            modal.querySelector('.image-modal-close').addEventListener('click', () => {
                modal.classList.remove('active');
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        }

        modal.querySelector('img').src = imageSrc;
        modal.classList.add('active');
    }

    /**
     * Update user online status
     */
    updateUserStatus(userId, isOnline) {
        const chatItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (chatItem) {
            const indicator = chatItem.querySelector('.online-indicator');
            if (isOnline && !indicator) {
                const avatar = chatItem.querySelector('.chat-avatar');
                const span = document.createElement('span');
                span.className = 'online-indicator';
                avatar.appendChild(span);
            } else if (!isOnline && indicator) {
                indicator.remove();
            }
        }
    }

    /**
     * Scroll to bottom
     */
    scrollToBottom(smooth = false) {
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTo({
                top: messagesContainer.scrollHeight,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    }

    /**
     * Show loading indicator
     */
    showLoadingIndicator() {
        const sentinel = document.getElementById('load-more-sentinel');
        if (sentinel) {
            sentinel.innerHTML = '<div class="loading-indicator"><div class="loading-spinner"></div></div>';
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoadingIndicator() {
        const sentinel = document.getElementById('load-more-sentinel');
        if (sentinel) {
            sentinel.innerHTML = '';
        }
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Disconnect
     */
    disconnect() {
        if (this.channel) {
            this.channel.presence.leave();
            this.channel.unsubscribe();
        }
        if (this.ably) {
            this.ably.close();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.querySelector('[data-chat-id]');
    if (chatContainer) {
        const chatId = chatContainer.dataset.chatId;
        const userId = chatContainer.dataset.userId;
        const isAdmin = chatContainer.dataset.isAdmin === 'true';

        window.chatRealtime = new ChatRealtime(chatId, userId, isAdmin);
    }
});