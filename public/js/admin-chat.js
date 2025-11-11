const config = window.ChatConfig || {};
const CSRF_TOKEN = config.csrfToken;
const USER_ID = config.userId;
const USER_ROLE = config.userRole;
const ABLY_KEY = config.ablyKey;

// Current chat state
let currentChatId = null;

     // ===== GLOBAL STATE =====
        let replyingTo = null;

        // ===== ELEMENTS =====
        const sidebar = document.getElementById('sidebar');
        const chatMain = document.getElementById('chatMain');
        const infoPanel = document.getElementById('infoPanel');
        const chatItems = document.querySelectorAll('.chat-item');
        const backBtn = document.getElementById('backBtn');
        const toggleInfo = document.getElementById('toggleInfo');
        const closeInfo = document.getElementById('closeInfo');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const messagesArea = document.getElementById('messagesArea');
        const searchInput = document.getElementById('searchInput');
        const filterTabs = document.querySelectorAll('.filter-tab');
        const replyBar = document.getElementById('replyBar');

        // ===== CHAT SELECTION =====
        chatItems.forEach(item => {
            item.addEventListener('click', function() {
                const chatId = this.getAttribute('data-chat-id');
                currentChatId = chatId;
                
                // Visual updates
                chatItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                chatMain.classList.add('active');
                
                // Update header
                const chatName = this.getAttribute('data-name');
                document.getElementById('chatUserName').innerHTML = chatName;
                
                // Load messages from database
                loadMessages(chatId);
                
                // Mobile handling
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('mobile-hidden');
                }
            });
        });

        // ===== REPLY FUNCTIONALITY =====
        function replyToMessage(messageId, author, text) {
            replyingTo = { messageId, author, text };
            
            document.getElementById('replyAuthor').textContent = author;
            document.getElementById('replyMessage').textContent = text;
            replyBar.classList.add('active');
            
            messageInput.focus();
        }

        function cancelReply() {
            replyingTo = null;
            replyBar.classList.remove('active');
        }

        // ===== SEND MESSAGE =====
        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text || !currentChatId) return;
            
            const data = {
                chat_id: currentChatId,
                message: text,
                reply_to_id: replyingTo?.messageId
            };
            
            try {
                const response = await fetch(config.routes.sendMessage.replace(':chatId', currentChatId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(data)
                });
                
                if (!response.ok) throw new Error('Failed to send message');
                
                const message = await response.json();
                
                // Ajouter le message à l'UI
                const messageDiv = createMessageElement(message);
                messagesArea.appendChild(messageDiv);
                
                // Clear input
                messageInput.value = '';
                sendBtn.classList.remove('active');
                cancelReply();
                scrollToBottom();
                
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Erreur lors de l\'envoi du message');
            }
        }
        async function loadMessages(chatId) {
            try {
                const url = config.routes.getMessages.replace(':chatId', chatId);
                const response = await fetch(url);
                
                if (!response.ok) throw new Error('Failed to load messages');
                
                const messages = await response.json();
                
                // Clear messages area
                messagesArea.innerHTML = '<div class="date-divider"><span class="date-badge">Aujourd\'hui</span></div>';
                
                // Render each message
                messages.forEach(message => {
                    const messageDiv = createMessageElement(message);
                    messagesArea.appendChild(messageDiv);
                });
                
                scrollToBottom();
                
            } catch (error) {
                console.error('Error loading messages:', error);
                alert('Erreur lors du chargement des messages');
            }
        }
        function createMessageElement(message) {
    const div = document.createElement('div');
    const isSent = message.is_admin;
    
    div.className = `message ${isSent ? 'sent' : 'received'}`;
    div.setAttribute('data-message-id', message.id);
    
    let replyHTML = '';
    if (message.reply_to) {
        replyHTML = `
            <div class="reply-preview">
                <div class="reply-author">${message.reply_to.user.firstname}</div>
                <div class="reply-text">${message.reply_to.message}</div>
            </div>
        `;
    }
    
    div.innerHTML = `
        ${!isSent ? `<img src="${message.user.path_photo}" class="message-avatar" onerror="this.onerror=null; this.src='/images/default-avatar.png';">` : ''}
        <div class="message-bubble">
            <div class="message-content">
                ${replyHTML}
                <p class="message-text">${message.message}</p>
            </div>
            <div class="message-footer">
                <span class="message-time">${formatTime(message.created_at)}</span>
                ${isSent ? `<i class="bi bi-check-all message-status ${message.readed ? 'read' : ''}"></i>` : ''}
            </div>
        </div>
        <div class="message-actions">
            <button class="message-action-btn" onclick="replyToMessage(${message.id}, '${message.user.firstname}', '${message.message.replace(/'/g, "\\'")}')">
                <i class="bi bi-reply"></i>
            </button>
        </div>
    `;
    
    return div;
}

// Helper function to format time
function formatTime(datetime) {
    const date = new Date(datetime);
    return date.getHours().toString().padStart(2, '0') + ':' + 
           date.getMinutes().toString().padStart(2, '0');
}
        // ===== MESSAGE INPUT =====
        messageInput.addEventListener('input', function() {
            if (this.value.trim()) {
                sendBtn.classList.add('active');
            } else {
                sendBtn.classList.remove('active');
            }
        });

        sendBtn.addEventListener('click', sendMessage);

        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // ===== NAVIGATION =====
        backBtn.addEventListener('click', () => {
            sidebar.classList.remove('mobile-hidden');
            if (window.innerWidth <= 768) {
                chatMain.classList.remove('active');
            }
        });

        toggleInfo.addEventListener('click', () => {
            infoPanel.classList.toggle('active');
        });

        closeInfo.addEventListener('click', () => {
            infoPanel.classList.remove('active');
        });

        // ===== SEARCH =====
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const allChats = document.querySelectorAll('.chat-list .chat-item');

            allChats.forEach(chat => {
                const name = chat.querySelector('.chat-name').textContent.toLowerCase();
                const message = chat.querySelector('.chat-message').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || message.includes(searchTerm)) {
                    chat.style.display = 'flex';
                } else {
                    chat.style.display = 'none';
                }
            });
        });

        // ===== FILTER TABS =====
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // ===== UTILITIES =====
        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function getCurrentTime() {
            const now = new Date();
            return now.getHours().toString().padStart(2, '0') + ':' + 
                   now.getMinutes().toString().padStart(2, '0');
        }

        // ===== WINDOW RESIZE =====
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-hidden');
                chatMain.classList.add('active');
            }
        });

        // ===== INITIALIZE =====
        scrollToBottom();
        console.log('MaxSkills Admin Chat - Enhanced with Reply & Image Fallbacks');
