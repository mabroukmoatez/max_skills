{{-- resources/views/admin/chats/index.blade.php --}}
@extends('layout.master')
@section('title', 'Chats')
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    @media (min-width: 768px) {
        .col-md-8 {
            flex: 0 0 auto;
            width: 66.666667%;
            max-height: 695px;
        } 
    }
        .app-wrapper .app-content {
            background : #eeeeee; 
        }
        .chat-container {
            height: calc(100vh - 100px);
            background: #F5F7FB;
            border-radius: 15px;
            overflow: hidden;
            height: 95vh !important;
            max-height: 100% !important;
        }

        .sidebar {
            background: #FFFFFF;
            height: 100%;
            overflow-y: auto;
            padding-left: 20px;
            padding-right: 20px;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #000000;
        }

        .chat-item {
            background: #FFFFFF;
            border: none;
            border-radius: 8px;
            transition: background 0.3s ease;
            color: #000000;
            padding: 10px;
            position: relative;
        }

        .chat-item:hover {
            background: #F5F7FB;
        }

        .chat-item.active {
            background: #dce8f6;
            color: #000000;
        }

        .nav-link.active {
            color: #ff6b02 !important;
        }

        .nav-link:not(.active) {
            color: #000000 !important;
        }

        .chat-item.active p,
        .chat-item.active small {
            color: #000000 !important;
        }

        .avatar {
            object-fit: cover;
        }

        .chat-window {
            height: 100%;
            background: #FFFFFF;
        }

        .card {
            border-radius: 0;
            overflow: hidden;
            border: none;
        }

        .message {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .messages::-webkit-scrollbar {
            width: 6px;
        }

        .messages::-webkit-scrollbar-track {
            background: #FFFFFF;
        }

        .messages::-webkit-scrollbar-thumb {
            background: #F8994F;
            border-radius: 10px;
        }

        .messages::-webkit-scrollbar-thumb:hover {
            background: #ff6b00;
        }

        .message-wrapper {
            display: flex;
            flex-direction: column;
            gap: 5px;
            max-width: 70%;
        }

        .sender-name {
            font-size: 0.95rem;
            font-weight: 500;
            color: #000000 !important;
            position:relative;
            top:-10px;
        }
        .message-content.bg-orange {
            top:-22px;
            right:-10px;
        }
        .message-content.bg-secondary-msg {
            top:-22px;
            left:-10px;
        }
        .message-content {
            border-radius: 12px;
            padding: 10px 15px;
            position: relative;
        }

        .message.sent .rounded-msg {
            border-radius: 6px 20px 20px 16px !important;
            margin-left: 36px !important;

        }

        .message-content.bg-orange {
            background: #2d83ff !important;
            color: #ffffff !important;
        }


        .message.received .rounded-msg {
            border-radius: 15px 0px 15px 15px !important;
            margin-right: 35px !important;
        }

        .message.sent {
            justify-content: start;
        }

        .message.received {
            justify-content: end;
        }

        .message-content.bg-orange {
            background: #ECEDEE;
            color: #000000;
        }

        .message.sent .d-flex {
            flex-direction: row;
        }

        .message.received .d-flex {
            flex-direction: row-reverse;
        }

        .message-content.bg-secondary-msg {
            background: #ffffff !important;
            color: #000000;
            box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.05);
        }

        .message-content.bg-secondary-msg-img img {
            border: 2px solid rgb(218 213 213 / 27%);
        }

        .timestamp {
            font-size: 0.75rem;
            text-align: right;
            color: #888888;
            display: block;
            top: -10px;
            position: relative;
        }

        .input-group input {
            background: #FFFFFF !important;
            color: #000000 !important;
            border-radius: 20px !important;
        }

        .input-group button {
            background: #F8994F !important;
            color: #000000 !important;
            border: none !important;
            border-radius: 20px !important;
        }

        .input-group button:hover {
            background: #ff6b00 !important;
        }

        .form-control:focus {
            background: #FFFFFF !important;
            border-color: #F8994F !important;
            box-shadow: none !important;
        }

        .input-group .form-control {
            border-radius: 20px !important;
            background: #F5F7FB !important;
            color: #000000 !important;
        }

        .input-group .btn-outline-secondary {
            background: #F5F7FB !important;
            border: none !important;
            border-radius: 20px !important;
        }

        .badge {
            background-color: #FF6B6B !important;
            font-size: 10px;
            border-radius: 10px;
            color: #FFFFFF;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin-bottom: 1rem;
        }

        .search-input {
            width: 100%;
            height: 50px;
            padding-left: 40px;
            padding-right: 50px;
            border: none;
            border-radius: 11px;
            font-size: 16px;
            color: #333;
            outline: none;
            font-weight: 100;
        }

        .search-input::placeholder {
            color: #888;
            font-size: 16px;
        }

        .search-icon-q {
            position: absolute;
            right: 20px;
            top: 50%;
            margin-right: 5px;
            transform: translateY(-50%);
            font-size: 24px;
            font-weight: bold;
            color: #333;
            opacity:0.5;
        }

        .search-input::placeholder {
            color: #000;
            font-size: 16px;
        }

        .placeholder-black::placeholder {
            color: #000 !important;
            font-size: 16px;
        }

        body {
            max-height: 100% !important;
            overflow: hidden !important;
        }

        footer {
            z-index: -1 !important;
        }

        .input-group-container:has(.chat-textarea:focus) {
            background-color: #ffffff !important;
        }

        .input-group-container {
            transition: background-color 0.3s ease;
        }

        textarea.form-control.chat-textarea::placeholder {
            color: #000000 !important;
        }

        .chat-textarea:focus {
            background-color: #ffffff !important;
        }

        .input-group-container:has(.chat-textarea:focus) {
            background-color: #ffffff !important;
        }

        .input-group-container {
            transition: background-color 0.3s ease;
        }

        textarea.form-control.chat-textarea::placeholder {
            color: #000000 !important;
        }

        .preview-item img {
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .reply-btn,
        .icon-group,
        .send-btn-group {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 999;
        }

        .reply-btn,
        .icon-group,
        .send-btn-group {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 999;
        }

        textarea.form-control.chat-textarea::placeholder {
            color: #000000 !important;
        }
        .card.h-100 {
            position: relative; 
            background-color: #f7f7f7;
            z-index: 1;
        }

        .card.h-100::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url(https://maxskills.tn/assets/images/background/bg_chat.png );
            background-size: inherit;
            background-position: center;
            opacity: 0.5; /* Set the opacity for the background image */
            z-index: -1; /* Places the pseudo-element behind the content */
        }
        .card .card-header {
            background-color:#ffffff !important;
        }
        #chatOptions {
            padding : 0 !important; 
        }
        .chat-card-header {
            padding: 10px;
        }
    </style>
@endsection
@section('main-content')
    <div style="padding-left:20px;margin-top: -2%;margin-right: 20px;">
        @livewire('chat-index')
        <div>
        @endsection
        @section('script')
            <script src="https://cdn.ably.com/lib/ably.min-2.js"></script>
            <script>
                document.addEventListener('livewire:init', () => {
                    console.log("Livewire et gestionnaires d'événements initialisés.");

                    // =================================================================
                    // GESTION PROPRE DE LA MODALE DE SUPPRESSION
                    // =================================================================
                    const modalElement = document.getElementById('deleteChatModal');

                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        Livewire.on('chatDeleted', () => {
                            console.log("Événement 'chatDeleted' reçu. Fermeture de la modale.");
                            modal.hide();
                            alert('Discussion supprimée avec succès.');
                        });
                        Livewire.on('error', (event) => {
                            console.log("Événement 'error' reçu. Fermeture de la modale.");
                            modal.hide();
                            const errorMessage = event.detail.message ||
                                'Une erreur est survenue. Vérifiez les logs.';
                            alert(errorMessage);
                        });

                    } else {
                        console.error("L'élément de la modale #deleteChatModal n'a pas été trouvé.");
                    }

                    let ably = null;
                    let channel = null;
                    let lastMessageId = 0;
                    let selectedChatId = null;

                    function scrollToBottom() {
                        const $chatMessages = $('#chat-messages');
                        if ($chatMessages.length) {
                            $chatMessages.scrollTop($chatMessages[0].scrollHeight);
                        }
                    }

                    function initializeAbly() {
                        ably = new Ably.Realtime({
                            authCallback: function(tokenParams, callback) {
                                $.ajax({
                                    url: '{{ route('ably.token') }}',
                                    method: 'GET',
                                    success: function(response) {
                                        console.log('Ably token fetched:', response);
                                        callback(null, response.token);
                                    },
                                    error: function(xhr) {
                                        console.error('Error fetching Ably token:', xhr
                                            .responseText);
                                        callback(new Error('Failed to fetch Ably token'), null);
                                    }
                                });
                            }
                        });

                        ably.connection.on('connected', function() {
                            console.log('Connected to Ably! Connection ID:', ably.connection.id);
                        });
                        ably.connection.on('failed', function(error) {
                            console.error('Ably connection failed:', error);
                            alert('Chat service unavailable. Please try again later.');
                        });
                        ably.connection.on('disconnected', function(error) {
                            console.error('Ably disconnected:', error);
                        });
                    }

                    function subscribeToMessages(chatId) {

                        if (ably && ably.connection.state === 'connected') {
                            channel = ably.channels.get(`chat:${chatId}`);
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
                                        appendMessage(msg);
                                        Livewire.dispatch('refreshChat');
                                    }
                                } catch (error) {
                                    console.error('Error processing message:', error, msg);
                                }
                            });
                            channel.subscribe(function(message) {
                                console.log('Received any message on channel:', `chat:${chatId}`, 'Event:', message
                                    .name,
                                    'Data:', message.data);
                            });
                        } else {
                            console.error('Ably not connected, retrying subscription in 1s (0)');
                            console.error(chatId);
                            console.error('Ably not connected, retrying subscription in 1s');
                            setTimeout(() => subscribeToMessages(chatId), 1000);
                        }
                    }

                    function appendMessage(msg) {
                        if (msg.id > lastMessageId) {
                            lastMessageId = msg.id;
                            const className = msg.is_admin ? 'sent' : 'received';
                            const alignClass = msg.is_admin ? 'justify-content-start' : 'justify-content-end';
                            const contentClass = msg.is_admin ? 'bg-orange text-black' : 'bg-secondary-msg text-black';
                            const readStatus = msg.readed || msg.is_admin ? '' : '';
                            const $chatMessages = $('#chat-messages');
                            const sentAt = new Date(msg.sent_at).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            $chatMessages.append(`
                    <div class="message ${className} mb-2 d-flex ${alignClass}" data-message-id="${msg.id}">
                        <div class="message-wrapper">
                            <div class="d-flex align-items-center gap-2">
                                <span class="sender-name">${msg.sender.firstname} ${msg.sender.name}</span>
                                <img src="https://maxskills.tn/${msg.sender.path_photo}" alt="Sender" class="message-avatar rounded-circle" style="width: 30px; height: 30px;">
                            </div>
                            <div class="message-content ${contentClass} p-2 rounded-msg position-relative" style="padding-left: 1rem!important; padding-right: .5rem!important; padding-bottom: .5rem!important; padding-top: .5rem!important">
                                <div>${msg.message}</div>
                                <small class="timestamp">${sentAt}</small>
                                ${readStatus}
                            </div>
                        </div>
                    </div>
                `);
                            scrollToBottom();
                        }
                    }
                    initializeAbly();

                   Livewire.on('chatSelected', (event) => {
    // On log l'événement pour voir sa structure exacte
    console.log("Événement 'chatSelected' reçu :", event);

    // On vérifie si les données sont dans event.detail (Livewire 3 standard)
    // ou directement dans event (compatibilité / cas particuliers)
    const data = event.detail || event;

    // On s'assure que les propriétés existent avant de les utiliser
    if (data.chatId !== undefined) {
        selectedChatId = data.chatId;
        lastMessageId = data.lastMessageId || 0;

        console.log('Chat sélectionné (corrigé):', selectedChatId, 'Dernier message ID:', lastMessageId);
        
        subscribeToMessages(selectedChatId);
        setTimeout(scrollToBottom, 200);
    } else {
        console.error("L'événement 'chatSelected' a été reçu mais ne contient pas de 'chatId'.", data);
    }
});

                    Livewire.on('messageSent', (event) => {
                        scrollToBottom();
                    });
                });
                document.addEventListener("DOMContentLoaded", function() {
                    $(document).on('click', '.chat-item', function() {
                        $(this).addClass('active').siblings().removeClass('active');
                    });
                });
            </script>

        @endsection