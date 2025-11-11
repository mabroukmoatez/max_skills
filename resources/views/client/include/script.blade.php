<script>
    // Initialize variables
    let ably = null;
    let channel = null;
    let chatId = null;
    let lastMessageId = 0;
    const pageType = 'chat'; // Set pageType to 'chat'
    const pageId = 1; // Set pageId to 1

    // --- NEW CODE TO APPLY ---
    const silentAudio = new Audio('data:audio/mp3;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAA1N3aXRjaCBQbHVzIMKpIE5DSCBTb2Z0d2FyZQBUSVQyAAAABgAAAzIyMzUAVFNTRQAAAA8AAANMYXZmNTcuODMuMTAwAAAAAAAAAAAAAAD/80DEAAAAA0gAAAAATEFNRTMuMTAwVVVVVVVVVVVVVUxBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQsRbAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQMSkAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV');
    const notificationSound = new Audio('https://maxskills.tn/client/audio/notif.mp3' );
    notificationSound.preload = 'auto';

    let isAudioUnlocked = false;
    let isInitialChatLoad = true;
    let hasPendingNotification = false; 

    // --- APPLY THIS CHANGE ---
    function unlockAudioContext() {
        if (isAudioUnlocked) return;
        silentAudio.play().then(() => {
            isAudioUnlocked = true;
            console.log("Audio context unlocked successfully. Notifications are ready.");

            // *** THE CRUCIAL FIX ***
            // If a notification arrived while audio was locked, play it now.
            if (hasPendingNotification) {
                console.log("Playing pending notification sound.");
                playNotificationSound();
                hasPendingNotification = false; // Reset the flag
            }
        }).catch(e => {
            console.warn("Could not unlock audio context on first interaction.", e);
        });
    }


    document.addEventListener('click', unlockAudioContext, { once: true });
   
    // Fetch Ably token
    $.ajax({
        url: '/ably/token', // Use the route name or URL
        method: 'GET',
        success: function(response) {
            console.log('Ably token fetched:', response.token);
            ably = new Ably.Realtime({
                token: response.token
            });
            initializeAbly();
        },
        error: function(xhr) {
            console.error('Error fetching Ably token:', xhr.responseText);
            alert('Failed to connect to chat service. Please try again later.');
        }
    });

    // Initialize Ably connection
    function initializeAbly() {
        ably.connection.on('connected', function() {
            console.log('Connected to Ably!');
            const chatInput = document.getElementById('chat-input');
            if (chatInput) {
                loadChat();
            }

        });
        ably.connection.on('failed', function(error) {
            console.error('Ably connection failed:', error);
        });
    }

    function toggleChat() {
        const chatWindow = $('#chat-window');
        const chatIcon = $('#chat-toggle-icon'); // Select the image by its new ID

        // Define the image sources
        const iconUp = 'https://maxskills.tn/client/svg/icon-robot.png'; // The new "up arrow" image
        const iconDown = 'https://maxskills.tn/client/svg/icon-robot.png'; // The original "down arrow" image

        if (chatWindow.is(':visible')) {
            // If chat is visible, we are about to hide it.
            chatWindow.hide();
            chatIcon.attr('src', iconDown); // Change icon back to "down arrow"
        } else {
            // If chat is hidden, we are about to show it.
            chatWindow.css('animation', 'none');
            chatWindow.show();
            chatWindow[0].offsetHeight; // Trigger reflow for animation
            chatWindow.css('animation', 'slideIn 0.3s ease-out');

            chatIcon.attr('src', iconUp); // Change icon to "up arrow"

            if (!chatId) {
                loadChat();
            }
            // Scroll to the bottom of the messages
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
        }
    }


    function loadChat() {
         isInitialChatLoad = true; 
        $.ajax({
            url: '/chat', // Use the route name or URL
            data: {
                page_type: pageType,
                page_id: pageId
            },
            success: function(response) {
                chatId = response.chat_id;
                console.log('Chat loaded, chatId:', chatId);
                displayMessages(response.messages);
                markMessagesAsRead();
                if (ably && ably.connection.state === 'connected' && chatId) {
                    channel = ably.channels.get(`chat:${chatId}`);
                    console.log('Subscribed to channel:', `chat:${chatId}`);
                    subscribeToMessages();
                }
                setTimeout(() => {
                    isInitialChatLoad = false;
                    console.log("Initial chat load complete. Notifications are now active.");
                }, 500);
            },
            error: function(xhr) {
                console.error('Error loading chat:', xhr.responseText);
                isInitialChatLoad = false;
            }
        });
    }

    function sendMessage() {
        const chatInput = document.getElementById('chat-input');
        const content = chatInput.value;
        
        if (!content.trim()) return;

        $.ajax({
            url: '/chat/send',
            method: 'POST',
            data: {
                chat_id: chatId,
                message: content,
                page_type: pageType,
                page_id: pageId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#chat-input').val('');
                if (channel) {
                    const messageData = {
                        id: lastMessageId + 1,
                        chat_id: chatId,
                        message: content,
                        is_admin: false,
                        sent_at: new Date().toISOString(),
                        sender_name: window.userData.name,
                        readed: false,
                        sender: {
                            id: window.userData.id,
                            name: window.userData.name,
                            firstname: window.userData.firstname,
                            path_photo: window.userData.path_photo
                        }
                    };
                    console.log('Publishing message to channel:', `chat:${chatId}`, messageData);
                    channel.publish('message', messageData, function(err) {
                        if (err) {
                            console.error('Error publishing message:', err);
                        } else {
                            console.log('Message published successfully');
                            displayMessages([messageData]);
                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('Error sending message:', xhr.responseText);
            }
        });
        chatInput.value = '';
        chatInput.style.height = 'auto';
        chatInput.style.height = (chatInput.scrollHeight) + 'px';
    }

   // --- NEW CODE TO APPLY ---
// --- APPLY THIS CHANGE ---
function playNotificationSound() {
    // If the audio is locked, we can't play the sound yet.
    // Instead, we set a flag and wait for the user to click.
    if (!isAudioUnlocked) {
        console.warn("Audio context is locked. Marking notification as pending.");
        hasPendingNotification = true;
        return;
    }

    // Don't play for the initial batch of messages.
    if (isInitialChatLoad) {
        console.log("Sound suppressed: Initial chat history is being loaded.");
        return;
    }

    // If we get here, it means audio is unlocked and it's a live message.
    console.log("All checks passed. Playing notification sound.");
    notificationSound.currentTime = 0;
    notificationSound.play().catch(error => {
        console.error("Error playing notification sound:", error);
    });
}


    function subscribeToMessages() {
        if (channel) {
            console.log('Subscribing to channel:', `chat:${chatId}`);
            channel.subscribe('message', function(message) {
                console.log('Raw message received:', message);
                console.log('Message event name:', message.name);
                console.log('Message data:', message.data);
                let msg = message.data;
                if (typeof msg === 'string') {
                    try {
                        msg = JSON.parse(msg);
                        console.log('Parsed message data:', msg);
                    } catch (error) {
                        console.error('Error parsing message data:', error, msg);
                        return;
                    }
                }
                console.log('Processed message on channel:', `chat:${chatId}`, msg);
                console.log('Current lastMessageId:', lastMessageId, 'Message ID:', msg.id);
                try {
                    if (!msg.id || !msg.chat_id || !msg.message) {
                        console.error('Invalid message structure:', msg);
                        return;
                    }
                    if (msg.id > lastMessageId) {
                        console.log('Displaying message with ID:', msg.id);
                        displayMessages([msg]);
                        markMessagesAsRead();
                        if (msg.is_admin) {
                            console.log("Admin message received. Playing notification sound.");
                            playNotificationSound();
                        } else {
                            console.log("Own message received. No sound will be played.");
                        }
                    } else {
                        console.log('Message filtered out due to ID:', msg.id, 'being <= lastMessageId:',
                            lastMessageId);
                    }
                } catch (error) {
                    console.error('Error processing message:', error, msg);
                }
            });
            channel.subscribe(function(message) {
                console.log('Received any message on channel:', `chat:${chatId}`, 'Event:', message.name,
                    'Data:', message.data);
            });
        } else {
            console.error('Cannot subscribe, channel is not initialized');
        }
    }

    function markMessagesAsRead() {
        if (chatId) {
            $.ajax({
                url: '/chat/read', // Use the route name or URL
                method: 'POST',
                data: {
                    chat_id: chatId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    console.error('Error marking messages as read:', xhr.responseText);
                }
            });
        }
    }
    const chatInput = document.getElementById('chat-input');

    if (chatInput) {
        document.getElementById('photo-icon').addEventListener('click', () => {
            document.getElementById('file-input').click();
        });


        // Handle file selection
        document.getElementById('file-input').addEventListener('change', async (event) => {
            const file = event.target.files[0];
            if (!file) return;
            const progressContainer = document.getElementById('upload-progress-container');
            const progressBar = document.getElementById('upload-progress-bar');

            // Validate file type (optional, since accept="image/*" already restricts)
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/avif', 'image/svg+xml',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, or GIF).');
                return;
            }

            // Validate file size (e.g., max 2MB)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                alert('File size exceeds 2MB limit.');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            // 1. AFFICHER LA BARRE DE PROGRESSION AVANT L'UPLOAD
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            // Upload the file
            try {

                // Reuse your existing uploadFile function
                const response = await uploadFile(formData, progressBar); // No progress bar for chat uploads
                const filePath = 'storage/' + response
                .path; // Assuming server returns file_path like "storage/uploads/..."
                const messageId = response.message_id; // Assuming server returns a message_id

                // Prepare message data for chat
                const messageData = {
                    id: messageId || lastMessageId + 1, // Use server-provided ID or increment
                    chat_id: chatId,
                    message: filePath,
                    is_admin: false,
                    sent_at: new Date().toISOString(),
                    sender_name: window.userData.name,
                    readed: false,
                    sender: {
                        id: window.userData.id,
                        name: window.userData.name,
                        firstname: window.userData.firstname,
                        path_photo: window.userData.path_photo
                    }
                };

                // Publish message to Ably channel
                if (channel) {
                    console.log('Publishing image message to channel:', `chat:${chatId}`, messageData);
                    channel.publish('message', messageData, (err) => {
                        if (err) {
                            console.error('Error publishing image message:', err);
                        } else {
                            console.log('Image message published successfully');
                            displayMessages([messageData]);
                            lastMessageId = Math.max(lastMessageId, messageData.id);
                        }
                    });
                }

                // Clear file input
                event.target.value = '';
            } catch (error) {
                console.error('Error uploading image:', error);
                alert('Failed to upload image. Please try again.');
            } finally {
                progressContainer.style.display = 'none';
                event.target.value = '';
            }
        });
    }

    function displayMessages(messages) {
        const chatMessages = $('#chat-messages');
        console.log('Attempting to display messages:', messages);
        messages.forEach(msg => {
            console.log('Processing message:', msg);
            if (msg.id > lastMessageId) {
                lastMessageId = msg.id;

                console.log('Message content:', msg.message, 'Type:', typeof msg.message);

                // Declare className at function scope
                let className = msg.is_admin ? 'message-admin' : 'message-client';
                // Check for file messages (both /storage/ and storage/)
                const isFileMessage = msg.message && (msg.message.startsWith('/storage/') || msg.message
                    .startsWith('storage/'));
                const extension = msg.message.split('.').pop().toLowerCase();
                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                const readStatus = msg.readed ? '' : ' ';
                let title = '';
                let messageContent = msg.message;
                if (isFileMessage && imageExtensions.includes(extension)) {
                    className += ' transparent-bg';
                    console.log('Applying transparent-bg for file message:', msg.message);
                } else {
                    messageContent = messageContent.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    className += ' msg-bg';
                    console.log('Applying msg-bg for non-file message:', msg.message);
                }

                if (msg.message.startsWith('storage/')) {
                    const extension = msg.message.split('.').pop().toLowerCase();
                    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'avif', 'svg', 'webp'];
                    const fileUrl = `https://maxskills.tn/${msg.message}`;
                    const fileName = msg.message.split('/').pop();

                    if (imageExtensions.includes(extension)) {
                        // Render image inline
                        messageContent =
                            `<img src="${fileUrl}" alt="Image" style="max-width: 200px; max-height: 200px; border-radius: 8px;" />`;
                    } else {
                        // Render download link for non-image files 
                        messageContent =
                            `<a href="${fileUrl}" style="text-decoration:none !important;" target="_blank" class="download-link"><img src="https://maxskills.tn/client/images/download_icon.png" style="font-size: 25px;width:35px;height:35px;"> Télécharger Fichier.${extension} <i class="fa-solid fa-download"></i></a>`;
                    }
                } else {
                    messageContent = messageContent.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    messageContent = `<div style="white-space: pre-wrap;">${messageContent}</div>`;

                }
                const html = `<div class="${className}" >${title}${messageContent}${readStatus}</div>`;
                console.log('Appending HTML to #chat-messages:', html);
                chatMessages.append(html);
                console.log('Scrolling chat window to bottom');
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
            } else {
                console.log('Message skipped due to ID:', msg.id, 'lastMessageId:', lastMessageId);
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
            }
        });

    }
    // ▼▼▼ AJOUTEZ CETTE NOUVELLE FONCTION ▼▼▼

async function uploadFile(formData, progressBar) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'https://maxskills.tn/uploadFile', true );
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        // Gère la progression de l'upload en temps réel
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable && progressBar) { // Vérifie que progressBar existe
                const percentComplete = (event.loaded / event.total) * 100;
                progressBar.style.width = `${percentComplete}%`;
            }
        };

        // Gère la fin de la requête
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    resolve(JSON.parse(xhr.response));
                } catch (e) {
                    reject(new Error('Réponse invalide du serveur.'));
                }
            } else {
                reject(new Error(`Échec du téléversement (statut: ${xhr.status})`));
            }
        };

        // Gère les erreurs réseau
        xhr.onerror = function() {
            reject(new Error('Erreur réseau lors du téléversement.'));
        };

        xhr.send(formData);
    });
}

    // async function uploadFile(formData, progressBar) {
    //     const xhr = new XMLHttpRequest();
    //     xhr.open('POST', 'https://maxskills.tn/uploadFile', true);
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    //     xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    //     xhr.upload.onprogress = function(event) {
    //         if (event.lengthComputable && progressBar) {
    //             const percentComplete = (event.loaded / event.total) * 100;
    //             progressBar.style.width = `${percentComplete}%`;
    //         }
    //     };
    //     return new Promise((resolve, reject) => {
    //         xhr.onload = function() {
    //             if (xhr.status >= 200 && xhr.status < 300) {
    //                 resolve(JSON.parse(xhr.response));
    //             } else {
    //                 reject(new Error('Upload failed'));
    //             }
    //         };
    //         xhr.onerror = function() {
    //             reject(new Error('Network error'));
    //         };
    //         xhr.send(formData);
    //     });
    // }
    // Handle Enter key for sending messages
    document.addEventListener('DOMContentLoaded', () => {
        const chatInput = document.getElementById('chat-input');
        if (chatInput) {
            chatInput.addEventListener('input', () => {
                chatInput.style.height = 'auto';
                chatInput.style.height = (chatInput.scrollHeight) + 'px';
            });

            // Gère l'envoi avec 'Entrée' et le retour à la ligne avec 'Maj+Entrée'
            chatInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    // Empêche le retour à la ligne par défaut
                    event.preventDefault();
                    // Envoie le message
                    sendMessage();
                }
                // Si Maj+Entrée est pressé, le comportement par défaut (retour à la ligne) se produit
            });
        }
    });
    // Notification Dropdown JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Get the notification dropdown element
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Store the last seen notification IDs to track new ones
        let lastSeenNotificationIds = [];

        // Function to get CSRF token from meta tag
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);

            // Check if date is today
            if (date.toDateString() === now.toDateString()) {
                return `Aujourd'hui à ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')} AM`;
            }

            // Check if date is yesterday
            if (date.toDateString() === yesterday.toDateString()) {
                return `Hier à ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')} AM`;
            }

            // Otherwise return day of week and date
            const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août',
                'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];

            return `${days[date.getDay()]} ${date.getDate()} ${months[date.getMonth()]} à ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')} AM`;
        }

        // Mark notifications as read when dropdown is opened
        notificationDropdown.addEventListener('show.bs.dropdown', function() {
            // Refresh notifications when dropdown is opened
            fetchNotifications(true);

            // AJAX call to mark all notifications as read
            fetch('/notifications/mark-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        all: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Notifications marked as read');

                    // Remove the badge after marking as read
                    const badge = notificationDropdown.querySelector('.notification-badge');
                    if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error marking notifications as read:', error);
                });
        });

        // Handle clicks on individual notification items
        document.addEventListener('click', function(event) {
            // Check if the clicked element is a notification item
            const notificationItem = event.target.closest('.notification-item');
            if (notificationItem) {
                // Get notification ID from data attribute
                const notificationId = notificationItem.dataset.notificationId;

                if (notificationId) {
                    // Mark specific notification as read
                    fetch('/notifications/mark-as-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                notification_id: notificationId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Notification marked as read');

                            // Remove unread indicator
                            const unreadIndicator = notificationItem.querySelector(
                                '.unread-indicator');
                            if (unreadIndicator) {
                                unreadIndicator.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error marking notification as read:', error);
                        });
                }

                // Navigate to link if present
                const link = notificationItem.dataset.link;
                if (link && link !== '#') {
                    window.location.href = link;
                }

                // Close the dropdown (optional)
                bootstrap.Dropdown.getInstance(notificationDropdown).hide();
            }

            // Handle close icon click
            if (event.target.closest('.close-icon')) {
                bootstrap.Dropdown.getInstance(notificationDropdown).hide();
            }
        });

        // Function to fetch notifications and update the dropdown
        function fetchNotifications(isDropdownOpen = false) {
            fetch('/notifications', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update notification badge count
                    const badge = notificationDropdown.querySelector('.notification-badge');

                    if (data.count > 0) {
                        // If there are unread notifications, show or update the badge
                        if (badge) {
                            badge.textContent = data.count;
                            badge.style.display = 'flex';
                        } else {
                            // Create badge if it doesn't exist
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.textContent = data.count;
                            notificationDropdown.appendChild(newBadge);
                        }
                    } else {
                        // If no unread notifications, hide the badge
                        if (badge) {
                            badge.style.display = 'none';
                        }
                    }

                    // Get current notification IDs
                    const currentNotificationIds = data.notifications.map(n => n.id);

                    // Determine new notifications (those not in lastSeenNotificationIds)
                    const newNotificationIds = currentNotificationIds.filter(id => !lastSeenNotificationIds
                        .includes(id));

                    // Update the dropdown content with notifications
                    updateNotificationDropdown(data.notifications, newNotificationIds, isDropdownOpen);

                    // Update lastSeenNotificationIds for next comparison
                    lastSeenNotificationIds = currentNotificationIds;
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
        }

        // Function to check for new notifications and update if needed
        function checkForNewNotifications() {
            fetch('/notifications/check-new', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update notification badge count
                    const badge = notificationDropdown.querySelector('.notification-badge');

                    if (data.count > 0) {
                        // If there are unread notifications, show or update the badge
                        if (badge) {
                            badge.textContent = data.count;
                            badge.style.display = 'flex';
                        } else {
                            // Create badge if it doesn't exist
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.textContent = data.count;
                            notificationDropdown.appendChild(newBadge);
                        }

                        // If dropdown is open, refresh the notifications list
                        const dropdownMenu = document.querySelector('.notification-dropdown-menu');
                        if (dropdownMenu.classList.contains('show')) {
                            fetchNotifications(true);
                        } else {
                            // If we have new notifications and dropdown is closed, fetch them in background
                            fetchNotifications(false);
                        }
                    } else {
                        // If no unread notifications, hide the badge
                        if (badge) {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking for new notifications:', error);
                });
        }

        // Function to update notification dropdown content
        function updateNotificationDropdown(notifications, newNotificationIds, isDropdownOpen) {
            const dropdownMenu = document.querySelector('.notification-dropdown-menu');

            // If dropdown is not open and this is not the initial load, don't update the UI
            if (!isDropdownOpen && lastSeenNotificationIds.length > 0) {
                return;
            }

            // Keep the header
            const header = dropdownMenu.querySelector('.notification-header');

            // Clear existing content except header
            dropdownMenu.innerHTML = '';

            // Add header back
            dropdownMenu.appendChild(header);

            if (notifications && notifications.length > 0) {
                // Add new notification items
                notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = 'notification-item';

                    // Add new-notification class if this is a new notification
                    if (newNotificationIds.includes(notification.id)) {
                        li.classList.add('new-notification');
                    }

                    li.dataset.notificationId = notification.id;
                    li.dataset.link = notification.link || '#';

                    // Create HTML structure for notification item with bell icon
                    li.innerHTML = `
                    <div class="notification-avatar">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${formatDate(notification.created_at)}</div>
                    </div>
                    ${notification.status === 0 ? '<div class="unread-indicator"></div>' : ''}
                `;

                    dropdownMenu.appendChild(li);
                });
            } else {
                // Show "no notifications" message
                const li = document.createElement('li');
                li.className = 'no-notifications';
                li.innerHTML = '<span>Pas des notifications</span>';
                dropdownMenu.appendChild(li);
            }
        }

        // Check for new notifications periodically (e.g., every 10 seconds)
        setInterval(checkForNewNotifications, 10000);

        // Initial fetch of notifications
        fetchNotifications(true);
        const dropdownToggles = document.querySelectorAll('.links-dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent the click from closing the modal
                const dropdown = this.closest('.links-dropdown');
                
                // Toggle the 'open' class on the main dropdown container
                dropdown.classList.toggle('open');
            });
        });

        // Add a global click listener to close the dropdown when clicking outside
        window.addEventListener('click', function(event) {
            const openDropdowns = document.querySelectorAll('.links-dropdown.open');
            openDropdowns.forEach(dropdown => {
                // If the click was outside the dropdown, close it
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('open');
                }
            });
        });
    });
</script>
