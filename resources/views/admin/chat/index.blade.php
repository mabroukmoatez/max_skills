<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaxSkills - Live Chat Interactive</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS - Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Toastr for Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    
    <!-- GLightbox for Image Preview -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    
    <!-- Emoji Picker CSS -->
    <style>
        :root {
            --primary-green: #00a884;
            --primary-green-hover: #008f72;
            --dark-bg: #111b21;
            --header-bg: #202c33;
            --sidebar-bg: #ffffff;
            --chat-bg: #efeae2;
            --message-incoming: #ffffff;
            --message-outgoing: #d9fdd3;
            --text-primary: #111b21;
            --text-secondary: #667781;
            --text-tertiary: #8696a0;
            --border-color: #e9edef;
            --hover-bg: #f5f6f6;
            --panel-header-bg: #f0f2f5;
            --badge-red: #ff3b30;
            --online-green: #25d366;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow: hidden;
            height: 100vh;
            background: var(--dark-bg);
        }

        /* ==================== HEADER ==================== */
        .app-header {
            background: var(--header-bg);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: relative;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .MaxSkills-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
        }

        .MaxSkills-icon {
            width: 32px;
            height: 32px;
            background: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .header-nav {
            display: flex;
            gap: 5px;
            margin-left: 30px;
        }

        .nav-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.05);
            color: #aebac1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .nav-btn:hover {
            background: rgba(255,255,255,0.1);
            transform: scale(1.05);
        }

        .nav-btn.active {
            background: var(--primary-green);
            color: white;
        }

        .nav-btn .badge-notification {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 18px;
            height: 18px;
            background: var(--badge-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            color: white;
            border: 2px solid var(--header-bg);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.05);
            padding: 5px 12px 5px 5px;
            border-radius: 25px;
            cursor: pointer;
            border: none;
            color: white;
            transition: all 0.2s;
        }

        .user-profile-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
        }

        .user-email {
            font-size: 12px;
            color: var(--text-tertiary);
        }

        /* ==================== MAIN LAYOUT ==================== */
        .main-container {
            display: flex;
            height: calc(100vh - 60px);
            background: white;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: 420px;
            background: white;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--panel-header-bg);
        }

        .sidebar-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .sidebar-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 6px;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 18px;
        }

        .icon-btn:hover {
            background: var(--hover-bg);
            transform: scale(1.1);
        }

        .filter-tabs {
            display: flex;
            gap: 6px;
        }

        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tab:hover {
            background: #e8f5e9;
            transform: translateY(-2px);
        }

        .filter-tab.active {
            background: #d8f3dc;
            color: var(--primary-green);
        }

        /* ==================== CHAT LIST ==================== */
        .chat-list {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .chat-section-divider {
            padding: 12px 20px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: capitalize;
        }

        .chat-section-divider i {
            font-size: 14px;
        }

        .chat-item {
            padding: 12px 20px;
            display: flex;
            gap: 15px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            align-items: center;
        }

        .chat-item:hover {
            background: var(--hover-bg);
            transform: translateX(5px);
        }

        .chat-item.active {
            background: #f0f2f5;
        }

        .chat-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 20px;
            left: 85px;
            height: 1px;
            background: var(--border-color);
        }

        .chat-item:last-child::after {
            display: none;
        }

        .chat-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            background: var(--primary-green);
            transition: all 0.3s;
        }

        .chat-item:hover .chat-avatar {
            transform: scale(1.1);
        }

        .online-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 14px;
            height: 14px;
            background: var(--online-green);
            border: 3px solid white;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(37, 211, 102, 0);
            }
        }

        .chat-info {
            flex: 1;
            min-width: 0;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .chat-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .verified-icon {
            color: #1da1f2;
            font-size: 16px;
            flex-shrink: 0;
        }

        .chat-time {
            font-size: 12px;
            color: var(--text-tertiary);
            white-space: nowrap;
        }

        .chat-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .chat-last-message {
            font-size: 14px;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .chat-last-message.typing {
            color: var(--primary-green);
            font-style: italic;
        }

        .typing-indicator {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }

        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: var(--primary-green);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-8px);
            }
        }

        .unread-count {
            background: var(--badge-red);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 12px;
            min-width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ==================== CHAT AREA ==================== */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--chat-bg);
        }

        .chat-header-bar {
            background: var(--panel-header-bg);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            height: 70px;
        }

        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 0;
        }

        .group-info {
            flex: 1;
            min-width: 0;
        }

        .group-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }

        .group-meta {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .online-count {
            color: var(--primary-green);
            font-weight: 500;
        }

        .header-action-btns {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: white;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 18px;
        }

        .action-btn:hover {
            background: var(--primary-green);
            color: white;
            transform: scale(1.1);
        }

        /* ==================== MESSAGES ==================== */
        .messages-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #efeae2;
            background-image: 
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(0,0,0,0.02) 10px,
                    rgba(0,0,0,0.02) 20px
                );
        }

        .date-separator {
            text-align: center;
            margin: 20px 0;
        }

        .date-badge {
            background: rgba(255,255,255,0.9);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            display: inline-block;
        }

        .message {
            margin-bottom: 8px;
            display: flex;
            gap: 10px;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.outgoing {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .message.outgoing .message-avatar {
            display: none;
        }

        .message-content {
            max-width: 65%;
            display: flex;
            flex-direction: column;
        }

        .message.outgoing .message-content {
            align-items: flex-end;
        }

        .message-bubble {
            background: white;
            border-radius: 8px;
            padding: 8px 12px;
            box-shadow: var(--shadow-sm);
            position: relative;
            transition: all 0.2s;
        }

        .message-bubble:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .message.outgoing .message-bubble {
            background: #d9fdd3;
        }

        .message-sender {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }

        .message.outgoing .message-sender {
            display: none;
        }

        .message-text {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.5;
            word-wrap: break-word;
        }

        .message-text .mention {
            color: #0099ff;
            font-weight: 500;
        }

        .message-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            margin-top: 4px;
        }

        .message-timestamp {
            font-size: 11px;
            color: var(--text-tertiary);
        }

        .message-status {
            font-size: 16px;
            color: #53bdeb;
        }

        .message-reactions-container {
            display: flex;
            gap: 6px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .reaction-bubble {
            background: rgba(0,0,0,0.05);
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 13px;
            border: 1px solid rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.2s;
        }

        .reaction-bubble:hover {
            background: rgba(0,0,0,0.1);
            transform: scale(1.1);
        }

        .message-actions {
            position: absolute;
            top: -30px;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            display: none;
            padding: 5px;
            gap: 5px;
        }

        .message-bubble:hover .message-actions {
            display: flex;
        }

        .message-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
        }

        .message-action-btn:hover {
            background: var(--hover-bg);
            color: var(--primary-green);
        }

        /* Voice Message */
        .voice-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
        }

        .voice-play-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--primary-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .voice-play-btn:hover {
            transform: scale(1.1);
        }

        .voice-waveform {
            flex: 1;
            height: 30px;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHg9IjAiIHk9IjEwIiB3aWR0aD0iMyIgaGVpZ2h0PSIxMCIgZmlsbD0iIzY2Nzc4MSIvPjxyZWN0IHg9IjUiIHk9IjUiIHdpZHRoPSIzIiBoZWlnaHQ9IjIwIiBmaWxsPSIjNjY3NzgxIi8+PHJlY3QgeD0iMTAiIHk9IjgiIHdpZHRoPSIzIiBoZWlnaHQ9IjE0IiBmaWxsPSIjNjY3NzgxIi8+PC9zdmc+') repeat-x;
        }

        .voice-duration {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Image Message */
        .message-image {
            max-width: 300px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-image:hover {
            transform: scale(1.02);
        }

        .message-image img {
            width: 100%;
            display: block;
        }

        /* ==================== MESSAGE INPUT ==================== */
        .message-input-area {
            background: var(--panel-header-bg);
            padding: 15px 20px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .input-actions {
            display: flex;
            gap: 8px;
        }

        .emoji-btn, .attach-btn, .voice-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 22px;
        }

        .emoji-btn:hover, .attach-btn:hover, .voice-btn:hover {
            background: var(--hover-bg);
            transform: scale(1.1);
        }

        .message-input-wrapper {
            flex: 1;
            position: relative;
        }

        .message-input {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 15px;
            outline: none;
            background: white;
            color: var(--text-primary);
            transition: all 0.2s;
            resize: none;
            max-height: 150px;
            min-height: 44px;
        }

        .message-input:focus {
            box-shadow: 0 0 0 2px var(--primary-green);
        }

        .send-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: var(--primary-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 20px;
        }

        .send-btn:hover {
            background: var(--primary-green-hover);
            transform: scale(1.1) rotate(15deg);
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        /* Voice Recording */
        .voice-recording {
            display: none;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            background: white;
            border-radius: 10px;
        }

        .voice-recording.active {
            display: flex;
        }

        .recording-indicator {
            width: 12px;
            height: 12px;
            background: var(--badge-red);
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50%, 100% {
                opacity: 1;
            }
            25%, 75% {
                opacity: 0.3;
            }
        }

        .recording-time {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .cancel-recording {
            margin-left: auto;
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background: var(--badge-red);
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cancel-recording:hover {
            transform: scale(1.05);
        }

        /* ==================== EMOJI PICKER ==================== */
        .emoji-picker-container {
            position: absolute;
            bottom: 70px;
            left: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            display: none;
            width: 350px;
            max-height: 400px;
            z-index: 1000;
        }

        .emoji-picker-container.show {
            display: block;
            animation: slideUpFade 0.3s ease;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .emoji-picker-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .emoji-picker-title {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
        }

        .emoji-categories {
            display: flex;
            gap: 5px;
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
        }

        .emoji-category-btn {
            padding: 8px 12px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.2s;
        }

        .emoji-category-btn:hover {
            background: var(--hover-bg);
        }

        .emoji-category-btn.active {
            background: var(--primary-green);
        }

        .emoji-grid {
            padding: 15px;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
        }

        .emoji-item {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .emoji-item:hover {
            background: var(--hover-bg);
            transform: scale(1.3);
        }

        /* ==================== ATTACHMENT MENU ==================== */
        .attachment-menu {
            position: absolute;
            bottom: 70px;
            left: 70px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            display: none;
            padding: 10px;
            z-index: 1000;
        }

        .attachment-menu.show {
            display: block;
            animation: slideUpFade 0.3s ease;
        }

        .attachment-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .attachment-option:hover {
            background: var(--hover-bg);
            transform: translateX(5px);
        }

        .attachment-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .attachment-icon.photos {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .attachment-icon.camera {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .attachment-icon.document {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .attachment-icon.contact {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .attachment-label {
            font-weight: 500;
            color: var(--text-primary);
        }

        /* ==================== RIGHT SIDEBAR ==================== */
        .right-sidebar {
            width: 400px;
            background: white;
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .right-sidebar-header {
            padding: 20px;
            background: var(--panel-header-bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .right-sidebar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .close-sidebar-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 18px;
        }

        .close-sidebar-btn:hover {
            background: var(--hover-bg);
            transform: rotate(90deg);
        }

        .right-sidebar-content {
            flex: 1;
            overflow-y: auto;
        }

        .community-profile {
            padding: 30px 20px;
            text-align: center;
            background: var(--panel-header-bg);
        }

        .community-avatar-large {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, #00c9a7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: 700;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            cursor: pointer;
        }

        .community-avatar-large:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .community-name-large {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .community-members-count {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .info-section {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .info-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .info-section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-section-title i {
            color: var(--text-secondary);
            font-size: 16px;
        }

        .edit-link {
            color: var(--primary-green);
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            border: none;
            background: transparent;
            font-weight: 500;
            transition: all 0.2s;
        }

        .edit-link:hover {
            text-decoration: underline;
            transform: scale(1.05);
        }

        .info-text {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .info-timestamp {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .toggle-switch {
            width: 50px;
            height: 28px;
            background: var(--primary-green);
            border-radius: 14px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            top: 2px;
            right: 2px;
            transition: all 0.3s;
            box-shadow: var(--shadow-sm);
        }

        .toggle-switch.off {
            background: #ccc;
        }

        .toggle-switch.off::after {
            right: calc(100% - 26px);
        }

        /* ==================== SCROLLBAR ==================== */
        .chat-list::-webkit-scrollbar,
        .messages-wrapper::-webkit-scrollbar,
        .right-sidebar-content::-webkit-scrollbar,
        .emoji-grid::-webkit-scrollbar {
            width: 6px;
        }

        .chat-list::-webkit-scrollbar-track,
        .messages-wrapper::-webkit-scrollbar-track,
        .right-sidebar-content::-webkit-scrollbar-track,
        .emoji-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-list::-webkit-scrollbar-thumb,
        .messages-wrapper::-webkit-scrollbar-thumb,
        .right-sidebar-content::-webkit-scrollbar-thumb,
        .emoji-grid::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 3px;
        }

        .chat-list::-webkit-scrollbar-thumb:hover,
        .messages-wrapper::-webkit-scrollbar-thumb:hover,
        .right-sidebar-content::-webkit-scrollbar-thumb:hover,
        .emoji-grid::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.3);
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1400px) {
            .right-sidebar {
                width: 350px;
            }
        }

        @media (max-width: 1200px) {
            .sidebar {
                width: 380px;
            }
            
            .right-sidebar {
                position: fixed;
                right: -100%;
                top: 0;
                height: 100vh;
                z-index: 1001;
                box-shadow: -4px 0 20px rgba(0,0,0,0.1);
            }
            
            .right-sidebar.show {
                right: 0;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1000;
                display: none;
            }
            
            .overlay.show {
                display: block;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: 60px;
                height: calc(100vh - 60px);
                z-index: 999;
                box-shadow: 4px 0 20px rgba(0,0,0,0.1);
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .message-content {
                max-width: 75%;
            }
            
            .user-info {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                max-width: 400px;
            }
            
            .right-sidebar {
                width: 100%;
            }
            
            .header-nav {
                margin-left: 10px;
            }
            
            .nav-btn {
                width: 36px;
                height: 36px;
            }
            
            .message-content {
                max-width: 85%;
            }

            .emoji-picker-container {
                width: 300px;
            }

            .emoji-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        @media (max-width: 576px) {
            .app-header {
                padding: 10px 15px;
            }
            
            .sidebar {
                width: 100%;
                max-width: 100%;
            }
            
            .MaxSkills-logo span {
                display: none;
            }
            
            .message-input-area {
                padding: 10px 15px;
            }
            
            .messages-wrapper {
                padding: 15px 10px;
            }

            .emoji-picker-container {
                width: calc(100vw - 40px);
                left: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- App Header -->
    <div class="app-header">
        <div class="header-left">
            <div class="MaxSkills-logo">
                <div class="MaxSkills-icon">
                    <i class="bi bi-MaxSkills"></i>
                </div>
                <span>MaxSkills</span>
            </div>
            
            <div class="header-nav">
                <button class="nav-btn">
                    <i class="bi bi-house-fill"></i>
                </button>
                <button class="nav-btn">
                    <i class="bi bi-telephone-fill"></i>
                </button>
                <button class="nav-btn active">
                    <i class="bi bi-chat-fill"></i>
                    <span class="badge-notification">5</span>
                </button>
                <button class="nav-btn">
                    <i class="bi bi-three-dots"></i>
                    <span class="badge-notification">1</span>
                </button>
                <button class="nav-btn">
                    <i class="bi bi-clock-history"></i>
                </button>
            </div>
        </div>
        
        <div class="header-right">
            <button class="icon-btn" id="themeToggle" title="Toggle theme">
                <i class="bi bi-brightness-high-fill"></i>
            </button>
            <button class="user-profile-btn">
                <div class="user-avatar">S</div>
                <div class="user-info">
                    <div class="user-name">Surendar</div>
                    <div class="user-email">sireniux@gmail.com</div>
                </div>
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <h2>Live Chat</h2>
                    <div class="header-actions">
                        <button class="icon-btn" title="New chat" id="newChatBtn">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="icon-btn" title="Search" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="discussions">
                        <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                        Discussions
                    </button>
                    <button class="filter-tab" data-filter="students">
                        <i class="bi bi-person-fill"></i>
                        Etudiant
                    </button>
                    <button class="filter-tab" data-filter="groups">
                        <i class="bi bi-people-fill"></i>
                        Groupes
                    </button>
                </div>
            </div>
            
            <div class="chat-list">
                <div class="chat-section-divider">
                    <i class="bi bi-pin-angle-fill"></i>
                    Discussions épinglées
                </div>
                
                <div class="chat-item active" data-aos="fade-right">
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);">
                            MO
                        </div>
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <div class="chat-name">
                                Maxskills Officielle
                                <i class="bi bi-patch-check-fill verified-icon"></i>
                            </div>
                            <span class="chat-time">3:31 PM</span>
                        </div>
                        <div class="chat-preview">
                            <div class="chat-last-message typing">
                                <span class="typing-indicator">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </span>
                                Muhammed typing...
                            </div>
                            <span class="unread-count">2</span>
                        </div>
                    </div>
                </div>
                
                <div class="chat-item" data-aos="fade-right" data-aos-delay="100">
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            S
                        </div>
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <div class="chat-name">Seetha</div>
                            <span class="chat-time">4:25 PM</span>
                        </div>
                        <div class="chat-preview">
                            <div class="chat-last-message">
                                <i class="bi bi-image"></i> images
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="chat-item" data-aos="fade-right" data-aos-delay="150">
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            E
                        </div>
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <div class="chat-name">Eleanor</div>
                            <span class="chat-time">3:10 PM</span>
                        </div>
                        <div class="chat-preview">
                            <div class="chat-last-message">Thanks for sharing figma fil...</div>
                            <span class="unread-count">2</span>
                        </div>
                    </div>
                </div>
                
                <div class="chat-item" data-aos="fade-right" data-aos-delay="200">
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            B
                        </div>
                        <span class="online-badge"></span>
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <div class="chat-name">Bartholomew</div>
                            <span class="chat-time">3:12 PM</span>
                        </div>
                        <div class="chat-preview">
                            <div class="chat-last-message">12 messages</div>
                        </div>
                    </div>
                </div>
                
                <div class="chat-section-divider">
                    <i class="bi bi-folder-fill"></i>
                    Toutes les discussions
                </div>
                
                <div class="chat-item" data-aos="fade-right" data-aos-delay="250">
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                            CM
                        </div>
                        <span class="online-badge"></span>
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <div class="chat-name">
                                Creative Minds
                                <i class="bi bi-patch-check-fill verified-icon"></i>
                            </div>
                            <span class="chat-time">4:31 PM</span>
                        </div>
                        <div class="chat-preview">
                            <div class="chat-last-message typing">
                                <span class="typing-indicator">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </span>
                                Visitor typing...
                            </div>
                            <span class="unread-count">2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header-bar">
                <div class="chat-header-left">
                    <button class="icon-btn d-none" id="toggleSidebar">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar" style="background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);">
                            M
                        </div>
                    </div>
                    <div class="group-info">
                        <div class="group-name">
                            Maxskills Officielle
                            <i class="bi bi-patch-check-fill verified-icon"></i>
                        </div>
                        <div class="group-meta">
                            8724 Members, <span class="online-count">243 Online</span>
                        </div>
                    </div>
                </div>
                <div class="header-action-btns">
                    <button class="action-btn" title="Voice call" id="voiceCallBtn">
                        <i class="bi bi-telephone-fill"></i>
                    </button>
                    <button class="action-btn" title="Video call" id="videoCallBtn">
                        <i class="bi bi-camera-video-fill"></i>
                    </button>
                    <button class="action-btn" title="Search" id="searchChatBtn">
                        <i class="bi bi-search"></i>
                    </button>
                    <button class="icon-btn" id="toggleRightSidebar">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
            </div>
            
            <div class="messages-wrapper" id="messagesWrapper">
                <div class="date-separator">
                    <span class="date-badge">Today</span>
                </div>
                
                <div class="message incoming" data-aos="fade-up">
                    <div class="message-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        B
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <div class="message-actions">
                                <button class="message-action-btn" title="Reply">
                                    <i class="bi bi-reply-fill"></i>
                                </button>
                                <button class="message-action-btn" title="React">
                                    <i class="bi bi-emoji-smile"></i>
                                </button>
                                <button class="message-action-btn" title="Forward">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                            <div class="message-sender">Bartholomew</div>
                            <div class="message-text">
                                Hey everyone, I'm excited to share the UI/UI design project I've been working on, called Project Chimera. Would you be willing to provide some feedback on the user flows and visual design? 😊
                            </div>
                            <div class="message-footer">
                                <span class="message-timestamp">2:31 AM</span>
                            </div>
                        </div>
                        <div class="message-reactions-container">
                            <span class="reaction-bubble" data-reaction="👍">👍 2</span>
                            <span class="reaction-bubble" data-reaction="😊">😊</span>
                        </div>
                    </div>
                </div>
                
                <div class="message incoming" data-aos="fade-up" data-aos-delay="100">
                    <div class="message-avatar" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                        E
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <div class="message-sender">Elia</div>
                            <div class="message-text">
                                This could be a great opportunity to put our UI skills to the test.
                            </div>
                            <div class="message-footer">
                                <span class="message-timestamp">2:31 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="message outgoing" data-aos="fade-up" data-aos-delay="150">
                    <div class="message-content">
                        <div class="message-bubble">
                            <div class="message-text">
                                Hey guys, just shared a new post on Instagram! 🎨
                            </div>
                            <div class="message-footer">
                                <span class="message-timestamp">2:31 AM</span>
                                <i class="bi bi-check-all message-status"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="message-input-area">
                <button class="emoji-btn" id="emojiBtn" title="Emoji">
                    <i class="bi bi-emoji-smile-fill"></i>
                </button>
                <button class="attach-btn" id="attachBtn" title="Attach">
                    <i class="bi bi-plus-circle-fill"></i>
                </button>
                <div class="message-input-wrapper">
                    <textarea class="message-input" id="messageInput" placeholder="Type a message" rows="1"></textarea>
                    <div class="voice-recording" id="voiceRecording">
                        <div class="recording-indicator"></div>
                        <div class="recording-time" id="recordingTime">0:00</div>
                        <button class="cancel-recording" id="cancelRecording">Cancel</button>
                    </div>
                </div>
                <button class="voice-btn" id="voiceBtn" title="Voice message">
                    <i class="bi bi-mic-fill"></i>
                </button>
                <button class="send-btn" id="sendBtn" title="Send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>

            <!-- Emoji Picker -->
            <div class="emoji-picker-container" id="emojiPicker">
                <div class="emoji-picker-header">
                    <span class="emoji-picker-title">Choose Emoji</span>
                    <button class="close-sidebar-btn" id="closeEmojiPicker">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="emoji-categories">
                    <button class="emoji-category-btn active" data-category="smileys">😊</button>
                    <button class="emoji-category-btn" data-category="gestures">👋</button>
                    <button class="emoji-category-btn" data-category="people">👨</button>
                    <button class="emoji-category-btn" data-category="animals">🐶</button>
                    <button class="emoji-category-btn" data-category="food">🍕</button>
                    <button class="emoji-category-btn" data-category="travel">✈️</button>
                    <button class="emoji-category-btn" data-category="activities">⚽</button>
                    <button class="emoji-category-btn" data-category="objects">💡</button>
                </div>
                <div class="emoji-grid" id="emojiGrid"></div>
            </div>

            <!-- Attachment Menu -->
            <div class="attachment-menu" id="attachmentMenu">
                <div class="attachment-option" data-type="photos">
                    <div class="attachment-icon photos">
                        <i class="bi bi-image-fill"></i>
                    </div>
                    <span class="attachment-label">Photos & Videos</span>
                </div>
                <div class="attachment-option" data-type="camera">
                    <div class="attachment-icon camera">
                        <i class="bi bi-camera-fill"></i>
                    </div>
                    <span class="attachment-label">Camera</span>
                </div>
                <div class="attachment-option" data-type="document">
                    <div class="attachment-icon document">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <span class="attachment-label">Document</span>
                </div>
                <div class="attachment-option" data-type="contact">
                    <div class="attachment-icon contact">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <span class="attachment-label">Contact</span>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="right-sidebar" id="rightSidebar">
            <div class="right-sidebar-header">
                <h3 class="right-sidebar-title">Community Info</h3>
                <button class="close-sidebar-btn" id="closeRightSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="right-sidebar-content">
                <div class="community-profile" data-aos="zoom-in">
                    <div class="community-avatar-large">
                        CM
                    </div>
                    <div class="community-name-large">
                        Creative Mind's
                        <i class="bi bi-patch-check-fill verified-icon"></i>
                    </div>
                    <div class="community-members-count">892 Members • 24 Online</div>
                </div>
                
                <div class="info-section" data-aos="fade-up">
                    <div class="info-section-header">
                        <div class="info-section-title">
                            <i class="bi bi-calendar-event"></i>
                            Created
                        </div>
                    </div>
                    <div class="info-timestamp">18-06-2025<br>10:01</div>
                </div>
                
                <div class="info-section" data-aos="fade-up" data-aos-delay="50">
                    <div class="info-section-header">
                        <div class="info-section-title">
                            <i class="bi bi-info-circle"></i>
                            Description
                        </div>
                        <button class="edit-link" id="editDescription">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div class="info-text">
                        Creative Mind's is a community hub launched in June 2025 to address this, I've created another community called Creative Mind's so that anyone can join.
                    </div>
                </div>
                
                <div class="info-section" data-aos="fade-up" data-aos-delay="100">
                    <div class="info-section-header">
                        <div class="info-section-title">
                            <i class="bi bi-bell-fill"></i>
                            Notification
                        </div>
                        <div class="toggle-switch" id="notificationToggle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS - Animate On Scroll -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Toastr for Notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    
    <!-- GLightbox for Image Preview -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 600,
            once: true,
            offset: 50
        });

        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        $(document).ready(function() {
            // Emoji Data
            const emojiData = {
                smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏'],
                gestures: ['👋', '🤚', '🖐', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝', '🙏'],
                people: ['👨', '👩', '👦', '👧', '🧒', '👶', '👴', '👵', '🧓', '👨‍🦰', '👩‍🦰', '👨‍🦱', '👩‍🦱', '👨‍🦳', '👩‍🦳', '👨‍🦲', '👩‍🦲', '🧔', '👱‍♂️', '👱‍♀️', '👨‍⚕️', '👩‍⚕️', '👨‍🎓', '👩‍🎓'],
                animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐽', '🐸', '🐵', '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇'],
                food: ['🍕', '🍔', '🍟', '🌭', '🍿', '🧈', '🍖', '🍗', '🥩', '🥓', '🍞', '🥖', '🥨', '🧀', '🥚', '🍳', '🧇', '🥞', '🧈', '🍯', '🥛', '🍼', '☕', '🍵', '🧃', '🥤', '🍶', '🍺', '🍻', '🥂'],
                travel: ['✈️', '🚀', '🛸', '🚁', '🛶', '⛵', '🚤', '🛳', '⛴', '🚢', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔'],
                activities: ['⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '⛳', '🏹', '🎣', '🥊', '🥋', '🎽', '🛹', '🛼', '🛷', '⛸', '🥌', '🎿', '⛷', '🏂'],
                objects: ['💡', '🔦', '🏮', '🪔', '📱', '💻', '⌨️', '🖥', '🖨', '🖱', '🖲', '💾', '💿', '📀', '📷', '📸', '📹', '🎥', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙', '🎚', '🎛', '🧭', '⏱', '⏰']
            };

            // Initialize Emoji Picker
            function loadEmojis(category = 'smileys') {
                const grid = $('#emojiGrid');
                grid.empty();
                
                emojiData[category].forEach(emoji => {
                    const emojiItem = $('<div class="emoji-item"></div>').text(emoji);
                    emojiItem.on('click', function() {
                        insertEmoji(emoji);
                    });
                    grid.append(emojiItem);
                });
            }

            // Insert Emoji
            function insertEmoji(emoji) {
                const input = $('#messageInput');
                const currentValue = input.val();
                const cursorPos = input[0].selectionStart;
                const newValue = currentValue.slice(0, cursorPos) + emoji + currentValue.slice(cursorPos);
                input.val(newValue);
                input.focus();
                
                // Move cursor after emoji
                const newPos = cursorPos + emoji.length;
                input[0].setSelectionRange(newPos, newPos);
            }

            // Load default emojis
            loadEmojis('smileys');

            // Emoji category selection
            $('.emoji-category-btn').on('click', function() {
                $('.emoji-category-btn').removeClass('active');
                $(this).addClass('active');
                const category = $(this).data('category');
                loadEmojis(category);
            });

            // Toggle Emoji Picker
            $('#emojiBtn').on('click', function(e) {
                e.stopPropagation();
                $('#emojiPicker').toggleClass('show');
                $('#attachmentMenu').removeClass('show');
            });

            $('#closeEmojiPicker').on('click', function() {
                $('#emojiPicker').removeClass('show');
            });

            // Toggle Attachment Menu
            $('#attachBtn').on('click', function(e) {
                e.stopPropagation();
                $('#attachmentMenu').toggleClass('show');
                $('#emojiPicker').removeClass('show');
            });

            // Close menus when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.emoji-picker-container, #emojiBtn').length) {
                    $('#emojiPicker').removeClass('show');
                }
                if (!$(e.target).closest('.attachment-menu, #attachBtn').length) {
                    $('#attachmentMenu').removeClass('show');
                }
            });

            // Attachment Options
            $('.attachment-option').on('click', function() {
                const type = $(this).data('type');
                $('#attachmentMenu').removeClass('show');
                
                if (type === 'photos') {
                    const input = $('<input type="file" accept="image/*,video/*" multiple>');
                    input.on('change', function(e) {
                        const files = e.target.files;
                        if (files.length > 0) {
                            toastr.success(`${files.length} file(s) selected!`);
                            // Handle file upload
                        }
                    });
                    input.click();
                } else if (type === 'camera') {
                    toastr.info('Camera feature - To be implemented');
                } else if (type === 'document') {
                    const input = $('<input type="file" accept=".pdf,.doc,.docx,.txt,.xlsx">');
                    input.on('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            toastr.success(`Document selected: ${file.name}`);
                        }
                    });
                    input.click();
                } else if (type === 'contact') {
                    toastr.info('Contact sharing - To be implemented');
                }
            });

            // Voice Recording
            let recordingInterval;
            let recordingSeconds = 0;
            let isRecording = false;

            $('#voiceBtn').on('click', function() {
                if (!isRecording) {
                    startRecording();
                } else {
                    stopRecording();
                }
            });

            function startRecording() {
                isRecording = true;
                recordingSeconds = 0;
                $('#messageInput').hide();
                $('#voiceRecording').addClass('active');
                $(this).find('i').removeClass('bi-mic-fill').addClass('bi-stop-circle-fill');
                
                toastr.info('Recording started...');
                
                recordingInterval = setInterval(() => {
                    recordingSeconds++;
                    const minutes = Math.floor(recordingSeconds / 60);
                    const seconds = recordingSeconds % 60;
                    $('#recordingTime').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
                }, 1000);
            }

            function stopRecording() {
                isRecording = false;
                clearInterval(recordingInterval);
                $('#messageInput').show();
                $('#voiceRecording').removeClass('active');
                $('#voiceBtn').find('i').removeClass('bi-stop-circle-fill').addClass('bi-mic-fill');
                
                const time = new Date().toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
                
                const voiceMessageHtml = `
                    <div class="message outgoing" data-aos="fade-up">
                        <div class="message-content">
                            <div class="message-bubble">
                                <div class="voice-message">
                                    <button class="voice-play-btn">
                                        <i class="bi bi-play-fill"></i>
                                    </button>
                                    <div class="voice-waveform"></div>
                                    <div class="voice-duration">0:${recordingSeconds.toString().padStart(2, '0')}</div>
                                </div>
                                <div class="message-footer">
                                    <span class="message-timestamp">${time}</span>
                                    <i class="bi bi-check-all message-status"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#messagesWrapper').append(voiceMessageHtml);
                scrollToBottom();
                AOS.refresh();
                
                toastr.success('Voice message sent!');
            }

            $('#cancelRecording').on('click', function() {
                stopRecording();
                toastr.warning('Recording cancelled');
            });

            // Send Message
            function sendMessage() {
                const messageText = $('#messageInput').val().trim();
                if (messageText) {
                    const time = new Date().toLocaleTimeString('en-US', { 
                        hour: 'numeric', 
                        minute: '2-digit',
                        hour12: true 
                    });
                    
                    const messageHtml = `
                        <div class="message outgoing" data-aos="fade-up">
                            <div class="message-content">
                                <div class="message-bubble">
                                    <div class="message-text">${escapeHtml(messageText)}</div>
                                    <div class="message-footer">
                                        <span class="message-timestamp">${time}</span>
                                        <i class="bi bi-check-all message-status"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#messagesWrapper').append(messageHtml);
                    $('#messageInput').val('');
                    scrollToBottom();
                    AOS.refresh();
                    
                    // Show notification
                    toastr.success('Message sent!');
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            $('#sendBtn').on('click', sendMessage);
            
            $('#messageInput').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Auto-resize textarea
            $('#messageInput').on('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            });

            // Toggle right sidebar
            $('#toggleRightSidebar').on('click', function() {
                $('#rightSidebar').toggleClass('show');
                $('#overlay').toggleClass('show');
            });
            
            $('#closeRightSidebar, #overlay').on('click', function() {
                $('#rightSidebar').removeClass('show');
                $('#overlay').removeClass('show');
            });
            
            // Toggle left sidebar on mobile
            $('#toggleSidebar').on('click', function() {
                $('#sidebar').toggleClass('show');
            });
            
            // Chat item selection
            $('.chat-item').on('click', function() {
                $('.chat-item').removeClass('active');
                $(this).addClass('active');
                
                if ($(window).width() <= 992) {
                    $('#sidebar').removeClass('show');
                }
            });
            
            // Filter tabs
            $('.filter-tab').on('click', function() {
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');
                const filter = $(this).data('filter');
                toastr.info(`Filtered by: ${filter}`);
            });
            
            // Toggle switch
            $('.toggle-switch').on('click', function() {
                $(this).toggleClass('off');
                const isOn = !$(this).hasClass('off');
                toastr.success(isOn ? 'Notifications enabled' : 'Notifications disabled');
            });

            // Action buttons
            $('#voiceCallBtn').on('click', function() {
                toastr.info('Starting voice call...');
            });

            $('#videoCallBtn').on('click', function() {
                toastr.info('Starting video call...');
            });

            $('#searchChatBtn').on('click', function() {
                toastr.info('Search in chat...');
            });

            $('#newChatBtn').on('click', function() {
                toastr.info('New chat...');
            });

            $('#searchBtn').on('click', function() {
                toastr.info('Search conversations...');
            });

            $('#editDescription').on('click', function() {
                toastr.info('Edit description...');
            });

            // Theme toggle
            let isDark = false;
            $('#themeToggle').on('click', function() {
                isDark = !isDark;
                $(this).find('i').toggleClass('bi-brightness-high-fill bi-moon-fill');
                if (isDark) {
                    toastr.info('Dark theme - To be implemented');
                } else {
                    toastr.info('Light theme active');
                }
            });

            // Reaction bubbles
            $(document).on('click', '.reaction-bubble', function() {
                const reaction = $(this).data('reaction');
                toastr.success(`You reacted with ${reaction}`);
            });

            // Message actions
            $(document).on('click', '.message-action-btn', function(e) {
                e.stopPropagation();
                const title = $(this).attr('title');
                toastr.info(`${title} action`);
            });

            // Scroll to bottom
            function scrollToBottom() {
                const wrapper = document.getElementById('messagesWrapper');
                wrapper.scrollTop = wrapper.scrollHeight;
            }

            scrollToBottom();
            
            // Responsive behavior
            function handleResize() {
                const width = $(window).width();
                
                if (width <= 992) {
                    $('#toggleSidebar').removeClass('d-none');
                } else {
                    $('#toggleSidebar').addClass('d-none');
                    $('#sidebar').removeClass('show');
                }
                
                if (width > 1200) {
                    $('#rightSidebar').removeClass('show');
                    $('#overlay').removeClass('show');
                }
            }
            
            $(window).on('resize', handleResize);
            handleResize();

            // Initialize GLightbox for images
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });

            console.log('🎉 MaxSkills Interactive Chat loaded successfully!');
            toastr.success('Welcome to MaxSkills Interactive Chat! 🎉');
        });
    </script>
</body>
</html>