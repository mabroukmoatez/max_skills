/**
 * Enhanced Chat Interface JavaScript
 * Additional features and improvements for the WhatsApp-style chat
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // AUTO-RESIZE TEXTAREA
    // ============================================
    const messageInput = document.querySelector('.message-input');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = '42px';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // ============================================
    // TYPING INDICATOR
    // ============================================
    let typingTimeout;
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            clearTimeout(typingTimeout);
            
            // Send typing start event
            if (window.Livewire && selectedChatId) {
                // Livewire.dispatch('userTyping', { chatId: selectedChatId });
            }
            
            // Clear typing after 2 seconds of inactivity
            typingTimeout = setTimeout(() => {
                // Livewire.dispatch('userStoppedTyping', { chatId: selectedChatId });
            }, 2000);
        });
    }

    // ============================================
    // SMOOTH SCROLL TO BOTTOM
    // ============================================
    function smoothScrollToBottom(element) {
        if (element) {
            element.scrollTo({
                top: element.scrollHeight,
                behavior: 'smooth'
            });
        }
    }

    // ============================================
    // MESSAGE ANIMATIONS
    // ============================================
    function animateNewMessage(messageElement) {
        messageElement.style.opacity = '0';
        messageElement.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            messageElement.style.transition = 'all 0.3s ease';
            messageElement.style.opacity = '1';
            messageElement.style.transform = 'translateY(0)';
        }, 10);
    }

    // ============================================
    // MOBILE MENU TOGGLE
    // ============================================
    const chatItems = document.querySelectorAll('.chat-item');
    const chatWindow = document.querySelector('.chat-window');
    
    chatItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768 && chatWindow) {
                chatWindow.classList.add('active');
                
                // Add back button for mobile
                addMobileBackButton();
            }
        });
    });

    function addMobileBackButton() {
        const chatHeader = document.querySelector('.chat-window-header');
        if (chatHeader && !document.querySelector('.mobile-back-btn')) {
            const backBtn = document.createElement('button');
            backBtn.className = 'mobile-back-btn';
            backBtn.innerHTML = '<i class="bi bi-arrow-left"></i>';
            backBtn.style.cssText = `
                background: none;
                border: none;
                color: #E9EDEF;
                font-size: 24px;
                cursor: pointer;
                margin-right: 8px;
                display: none;
            `;
            
            if (window.innerWidth <= 768) {
                backBtn.style.display = 'block';
            }
            
            backBtn.addEventListener('click', function() {
                chatWindow.classList.remove('active');
            });
            
            chatHeader.prepend(backBtn);
        }
    }

    // ============================================
    // EMOJI PICKER PLACEHOLDER
    // ============================================
    const emojiBtn = document.querySelector('.input-icon[title="Emoji"]');
    if (emojiBtn) {
        emojiBtn.addEventListener('click', function() {
            // Placeholder for emoji picker integration
            console.log('Emoji picker clicked - integrate your preferred emoji library here');
            
            // Example: Show a simple emoji selector
            // showEmojiPicker();
        });
    }

    // ============================================
    // FILE PREVIEW BEFORE UPLOAD
    // ============================================
    const fileInput = document.getElementById('file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                showFilePreview(files);
            }
        });
    }

    function showFilePreview(files) {
        // Create preview container if it doesn't exist
        let previewContainer = document.querySelector('.file-preview-container');
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.className = 'file-preview-container';
            previewContainer.style.cssText = `
                position: fixed;
                bottom: 80px;
                left: 50%;
                transform: translateX(-50%);
                background: #0F1419;
                border: 1px solid #202C33;
                border-radius: 12px;
                padding: 16px;
                max-width: 400px;
                z-index: 1000;
            `;
            document.body.appendChild(previewContainer);
        }

        previewContainer.innerHTML = '<h5 style="color: #E9EDEF; margin-bottom: 12px;">Files to Upload</h5>';
        
        Array.from(files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.style.cssText = `
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px;
                background: #1F2C34;
                border-radius: 8px;
                margin-bottom: 8px;
            `;
            
            const fileIcon = getFileIcon(file.type);
            const fileSize = formatFileSize(file.size);
            
            fileItem.innerHTML = `
                <i class="bi ${fileIcon}" style="font-size: 24px; color: #00A884;"></i>
                <div style="flex: 1;">
                    <div style="color: #E9EDEF; font-size: 14px;">${file.name}</div>
                    <div style="color: #8696A0; font-size: 12px;">${fileSize}</div>
                </div>
            `;
            
            previewContainer.appendChild(fileItem);
        });

        // Auto-hide after 3 seconds
        setTimeout(() => {
            if (previewContainer) {
                previewContainer.remove();
            }
        }, 3000);
    }

    function getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) return 'bi-file-image';
        if (mimeType.startsWith('video/')) return 'bi-file-play';
        if (mimeType.startsWith('audio/')) return 'bi-file-music';
        if (mimeType.includes('pdf')) return 'bi-file-pdf';
        if (mimeType.includes('word')) return 'bi-file-word';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'bi-file-excel';
        return 'bi-file-earmark';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // ============================================
    // ONLINE STATUS SIMULATOR
    // ============================================
    function updateOnlineStatus() {
        // This would typically come from your backend
        // For now, it's just a visual indicator
        const onlineIndicators = document.querySelectorAll('.online-indicator');
        onlineIndicators.forEach(indicator => {
            indicator.style.animation = 'pulse 2s infinite';
        });
    }

    // Add pulse animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    `;
    document.head.appendChild(style);

    // ============================================
    // CONTEXT MENU FOR MESSAGES
    // ============================================
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.message')) {
            e.preventDefault();
            showMessageContextMenu(e);
        }
    });

    function showMessageContextMenu(e) {
        // Remove existing context menu
        const existingMenu = document.querySelector('.message-context-menu');
        if (existingMenu) {
            existingMenu.remove();
        }

        const menu = document.createElement('div');
        menu.className = 'message-context-menu';
        menu.style.cssText = `
            position: fixed;
            top: ${e.clientY}px;
            left: ${e.clientX}px;
            background: #1F2C34;
            border: 1px solid #202C33;
            border-radius: 8px;
            padding: 8px 0;
            z-index: 2000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        `;

        const options = [
            { icon: 'bi-reply', text: 'Reply', action: () => console.log('Reply') },
            { icon: 'bi-forward', text: 'Forward', action: () => console.log('Forward') },
            { icon: 'bi-star', text: 'Star', action: () => console.log('Star') },
            { icon: 'bi-trash', text: 'Delete', action: () => console.log('Delete'), danger: true }
        ];

        options.forEach(option => {
            const item = document.createElement('div');
            item.style.cssText = `
                padding: 10px 16px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 12px;
                color: ${option.danger ? '#DC3545' : '#E9EDEF'};
                transition: background 0.2s;
            `;
            item.innerHTML = `
                <i class="bi ${option.icon}"></i>
                <span>${option.text}</span>
            `;
            item.addEventListener('mouseenter', () => {
                item.style.background = '#2A3942';
            });
            item.addEventListener('mouseleave', () => {
                item.style.background = 'transparent';
            });
            item.addEventListener('click', () => {
                option.action();
                menu.remove();
            });
            menu.appendChild(item);
        });

        document.body.appendChild(menu);

        // Remove menu when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function removeMenu() {
                menu.remove();
                document.removeEventListener('click', removeMenu);
            });
        }, 10);
    }

    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-box input');
            if (searchInput) {
                searchInput.focus();
            }
        }

        // Escape: Clear selection or close modals
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                bootstrap.Modal.getInstance(activeModal).hide();
            }
        }

        // Ctrl/Cmd + Enter: Send message
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && messageInput) {
            e.preventDefault();
            const sendBtn = document.querySelector('.send-btn');
            if (sendBtn) {
                sendBtn.click();
            }
        }
    });

    // ============================================
    // LOAD MORE MESSAGES ON SCROLL TOP
    // ============================================
    const messagesContainer = document.getElementById('chat-messages');
    if (messagesContainer) {
        messagesContainer.addEventListener('scroll', function() {
            if (this.scrollTop === 0) {
                // Load more messages when scrolled to top
                console.log('Load more messages');
                // Implement pagination here
            }
        });
    }

    // ============================================
    // NETWORK STATUS INDICATOR
    // ============================================
    window.addEventListener('online', function() {
        showNetworkStatus('Connected', 'success');
    });

    window.addEventListener('offline', function() {
        showNetworkStatus('Disconnected', 'danger');
    });

    function showNetworkStatus(message, type) {
        const statusBar = document.createElement('div');
        statusBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: ${type === 'success' ? '#00A884' : '#DC3545'};
            color: white;
            text-align: center;
            padding: 8px;
            z-index: 9999;
            font-size: 14px;
        `;
        statusBar.textContent = message;
        document.body.appendChild(statusBar);

        setTimeout(() => {
            statusBar.remove();
        }, 3000);
    }

    // ============================================
    // INITIALIZE
    // ============================================
    updateOnlineStatus();
    
    console.log('✅ Enhanced chat features loaded');
});
