<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.css') }}" >

   <style>
    * {
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }
    body {
        background-color: #010211;
        color: white;
        scrollbar-width: auto; /* For Firefox */
        -ms-overflow-style: none; 
        overflow-x: hidden;
    }

    body::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    .dropdown-menu.dropdown-menu-end {
        margin : 10px!important;
    }
    .dropdown-menu {
        background-color: #141414; 
        border: none; 
        padding: 0.5rem 0;
    }
    .dropdown-item {
        color:#B3B3B3 !important;
        padding: 0.5rem 0.5rem; 
        display: flex; 
        align-items: center; 
        text-decoration: none;
        font-weight : lighter; 
    }
    .dropdown-item:hover {
        background-color: #333;
    }
    .dropdown-item .icon {
        margin-right: 0.5rem; 
        font-size: 1rem; 
    }
    .dropdown-item-last a,.dropdown-item a{
        font-weight : 300; 
    }
    .dropdown-item-last {
        color:#B3B3B3 !important;
        display: flex; 
        align-items: center; 
        text-decoration: none;
        font-weight : lighter; 
        flex-direction : column;
    }
    .dropdown-item-last:hover {
        background-color: #333;
    }
    
    .dropdown-divider {
        border-color: #333;
        margin: 0.5rem 0;
    }
    .promo-bar {
        background-color: #F8994F;
        color: white;
        padding: 12px 0;
        text-align: center; 
        font-size: 0.9rem;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 1000;
    }
    .nav-row {
        position: absolute;
        top: 48px;
        width: 100%;
        padding: 30px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 999;
    }

    @media (max-width: 499px) { 
        .nav-row img {
            height: 30px;
        }
        .col-lg-2-4 {
            flex: 0 0 100%; 
            max-width: 100%;
            margin-bottom: 10px;
        }
    }
    
    @media (min-width: 500px) and (max-width: 650px) { 
        .nav-row img {
            height: 30px;
        }
        .col-lg-2-4 {
            flex: 0 0 50%; /* Two items per row */
            max-width: 50%;
            margin-bottom: 10px; /* Space between items */
        }
    }

    @media (min-width: 651px) and (max-width: 769px) { 
        .nav-row img {
            height: 30px;
        }
        .col-lg-2-4 {
            flex: 0 0 33.33%; /* Three items per row */
            max-width: 33.33%;
            margin-bottom: 10px; /* Space between items */
        }
    }
    @media (min-width: 769px) { 
        .nav-row img {
            height: 50px;
        }
        .col-lg-2-4 {
            flex: 0 0 20%; 
            max-width: 20%;
        }
        
    }
   /* Notification and Profile Container */
.dropdown-container {
    display: flex;
    align-items: center;
    gap: 30px;
}

/* Notification Icon Styling */
.notification-icon {
    position: relative;
    cursor: pointer;
    color: white;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-icon i {
    color: white;
}

/* Notification Badge Styling */
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #e63946; /* Red color for the badge */
    color: white;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    font-weight: bold;
}

/* Notification Dropdown Menu */
.notification-dropdown-menu {
    background-color: #141414;
    border: none;
    padding: 0;
    min-width: 350px;
    max-width: 400px;
    max-height: 500px;
    overflow-y: auto;
    margin-top: 10px !important;
    border-radius: 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    scrollbar-width: thin;
    scrollbar-color: #141414 #333333;
}

/* Scrollbar styles for WebKit browsers (Chrome, Safari, Edge) */
.notification-dropdown-menu::-webkit-scrollbar {
    width: 8px;
}

.notification-dropdown-menu::-webkit-scrollbar-track {
    background: #141414;
}

.notification-dropdown-menu::-webkit-scrollbar-thumb {
    background: #333333;
    border-radius: 4px;
}

.notification-dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: #555555;
}
/* Notification Header */
.notification-header {
    padding: 15px;
    border-bottom: 1px solid #2a2a2a;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    color: white;
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

.notification-header .close-icon {
    color: #999;
    cursor: pointer;
}

/* Notification Item */
.notification-item {
    padding: 15px;
    border-bottom: 1px solid #2a2a2a;
    display: flex;
    align-items: flex-start;
    position: relative;
    transition: background-color 0.3s ease;
}

.notification-item:last-child {
    border-bottom: none;
}

/* New Notification Highlight */
.notification-item.new-notification {
    background-color: rgba(255, 87, 34, 0.15);
    animation: fadeHighlight 5s forwards;
}

@keyframes fadeHighlight {
    0% { background-color: rgba(255, 87, 34, 0.15); }
    100% { background-color: transparent; }
}

/* Notification Avatar */
.notification-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

/* Notification Content */
.notification-content {
    flex-grow: 1;
}

.notification-title {
    color: white;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
    line-height: 1.4;
}

.notification-message {
    color: #999;
    font-size: 13px;
    margin-bottom: 5px;
    line-height: 1.4;
}

.notification-time {
    color: #666;
    font-size: 12px;
}

/* Unread Indicator */
.unread-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #ff5722;
    position: absolute;
    top: 20px;
    right: 15px;
}

/* No Notifications Message */
.no-notifications {
    color: #999;
    padding: 20px;
    text-align: center;
    font-size: 14px;
}
    .profile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .profile-icon img {
        border-radius:50%;
    }
    .hero-section {
        background-position: center;
        background-repeat: no-repeat;
        min-height: 90vh;
        display: flex;
        justify-content: flex-start;
        align-items: flex-end;
        position: relative;
        margin-bottom : -3%;
    }
    .hero-content {
        max-width: 600px;
        text-align: center;
        margin-left: 10%;
        margin-bottom: 10%;
        margin-top: 10%;
    }
    .custom-btn {
        background-color: #F8994F !important;
        color: black !important;
        font-weight: 600;
        border: none;
        margin-top: 20px;
        border-radius:50px;
    }

    .card-section {
        padding: 0px 20px;
    }
    .chapter-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin-bottom: 30px;
        border: none;
        transition: transform 0.3s;
        background-color: rgba(255, 255, 255, 0);
    }
    .card-img-container {
        flex: 0 0 auto;
        width: 100%;
        height: 85%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chapter-card img {
        width: 100%;
        height: 92%;
        object-fit: fill;
        border:1px solid #7cc4f32e;
        border-radius:5%;
        max-height:295px;
    }
    .card-body-chapter {
        height: 15%;
        text-align: center;
        flex: 1; 
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0rem;
        margin-top: 0;
    }
    .chapter-card:hover {
        transform: translateY(-5px);
    }
    .card-img-top {
        width: auto;
        height: 100%;
        object-fit: contain;
    }
    .card-title {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: visible;
        text-overflow: unset;
        margin-bottom: 0;
        line-height: 1.2;
        min-height: 2.4em;
        text-align: center;
        font-weight: 500;
        color: white;
    }

    .orange-box {
        width: auto; 
        height: auto; 
        padding: 0px 5px; 
        background: #f8994f3b; 
        border: 0.1px solid #F8994F; 
        border-radius: 5px;
        position: relative;
        display: inline-block;
        vertical-align: middle; 
    }
    .orange-box .corner-circle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #F8994F;
        border-radius: 50%;
    }
    .orange-box .top-left {
        top: -5px;
        left: -5px;
    }
    .orange-box .top-right {
        top: -5px;
        right: -5px;
    }
    .orange-box .bottom-left {
        bottom: -5px;
        left: -5px;
    }
    .orange-box .bottom-right {
        bottom: -5px;
        right: -5px;
    }
    .orange-box .arrow {
        position: absolute;
        bottom: 5px;
        right: -20px;
        width: 15%;
        height: 30%;
        background: url('/client/images/mouse.png') no-repeat center;
        background-size: contain;
    }
    .footer {
        background-color: #050C21;
        color: #FFFFFF8F;
        text-align: center;
    }

/* Rangées du footer */
.footer-row,
.footer-row-second {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 3%;
}

/* Logo */
.footer-logo img {
    height: 40px; /* Ajustez la taille du logo */
}
.footer-links ul li a:hover {
    text-decoration: underline;
}
/* Liens */
.footer-links ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px; /* Espace entre les liens */
}

.footer-links ul li {
    display: inline;
}

.footer-links ul li a {
    color: #FFFFFF8F;
    text-decoration: none;
}

.footer-links ul li a:hover {
    text-decoration: underline;
}

/* Formulaire d'abonnement */
.footer-subscribe {
    display: flex;
    align-items: center;
    background: #FFFFFF1A; /* Background color for the input */
    border-radius: 50px; /* Rounded corners */
    padding: 4px; /* Space inside the wrapper */
    border: 1px solid #444;
}

.footer-subscribe input {
    flex: 1; /* Take up remaining space */
    padding: 8px;
    border: none;
    color: white; /* Text color */
    outline: none;
    background: transparent;
}
.footer-subscribe button {
    padding: 8px 16px;
    background-color: #FFFFFF26; /* Orange background */
    color: white;
    border: 1px solid #FFFFFF45;
    border-radius: 50px; /* Rounded corners */
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.footer-subscribe button:hover {
    background-color: #F8994F;
}

/* Copyright */
.footer-copyright p {
    margin: 0;
    font-size: 14px;
}
@media (min-width: 769px) { 
     .hero-section { 
        background-size: 100% 100%;
    }
}
@media (max-width: 768px) {
    .chat-window {
        width:100% !important;
        right:0px !important;
    }
    .card-body-chapter {
        margin-top:10px;
    }
    .card-img-container {
        flex: 0 1 auto;
    }
    .card-title {
        font-size:larger !important;
    }
    .chapter-card img {
        height: 100%;
        max-height: 450px;
    }
    .container.card-section .col-lg-2-4.col-md-6.col-12.mb-4 {
        margin-bottom : 0.6rem !important;
    }
    .hero-content {
        margin-left: 0%;
        padding-right: 25px;
        padding-left: 25px;
    }
    .hero-section { 
        min-height : 10vh;
        margin-top: 15%;
        padding-top: 15%;
    }
    .dropdown-container {
        gap: 15px;
    }
    
    .notification-icon {
        font-size: 20px;
    }
    
    .notification-badge {
        min-width: 18px;
        height: 18px;
        font-size: 10px;
    }
    .profile-icon img {
        width: 35px;
        height: 35px;
    }
    .footer-row,
    .footer-row-second {
        flex-direction: column; /* Stack elements vertically */
        gap: 15px; /* Space between stacked elements */
        padding: 10px 5%; /* Adjust padding for smaller screens */
    }

    .footer-links ul {
        flex-direction: column; /* Stack links vertically */
        gap: 10px; /* Space between links */
    }

    .footer-subscribe {
        width: 100%;
    }

    .footer-copyright {
        text-align: center; /* Center-align copyright text */
    }
    .footer-links ul li:nth-child(2),
    .footer-links ul li:nth-child(4) {
        display: none; /* Hide the elements */
    }
}

@media (max-width: 480px) {
    .footer-links ul li:nth-child(2),
    .footer-links ul li:nth-child(4) {
        display: none; /* Hide the elements */
    }
    .footer-logo img {
        height: 30px; /* Smaller logo for very small screens */
    }

    .footer-subscribe {
        flex-direction: row;
    }

    .footer-subscribe input {
        width: 100%; /* Full width for input and button */
    }
    ,
    .footer-subscribe button {
        width: 35%; 
    }

    .footer-copyright p {
        font-size: 12px; /* Smaller font size for very small screens */
    }
}
</style>
<style>
    /* Chat Bubble */
    .chat-bubble {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #F8994F, #ff6b00);
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(248, 153, 79, 0.4);
        z-index: 1000;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        font-size: 24px;
    }

    .chat-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(248, 153, 79, 0.6);
    }

    /* Chat Window */
    .chat-window {
        display: none;
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 400px;
        height: 550px;
        background: linear-gradient(180deg, #030737 0%, #0A139D 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
        animation: slideIn 0.3s ease-out;
    }

    /* Slide-in animation for chat window */
    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Chat Top Bar */
    .chat-top-bar {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background: linear-gradient(180deg, #030737 0%, #0A139D 100%); /* Matching chat window gradient */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .chat-top-bar .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .chat-top-bar .title {
        flex: 1;
        color: #fff;
        font-size: 1rem;
        font-weight: 500;
    }

    .chat-top-bar .status {
        display: flex;
        align-items: center;
        color: #A0AEC0;
        font-size: 0.8rem;
    }

    .chat-top-bar .status::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #00FF00; /* Green dot for online status */
        border-radius: 50%;
        margin-right: 5px;
    }

    .chat-top-bar .info-icon {
        color: #A0AEC0;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .chat-top-bar .info-icon:hover {
        color: #fff;
    }

    /* Chat Messages Area */
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #F8994F #030737;
    }

    /* Webkit scrollbar styling */
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #030737;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #F8994F;
        border-radius: 10px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #ff6b00;
    }

    /* Message Styling */
    .message-client, .message-admin {
        margin: 8px 10px;
        padding: 10px 15px;
        border-radius: 12px;
        max-width: 80%;
        font-size: 0.95rem;
        line-height: 1.4;
        position: relative;
        transition: background 0.3s ease;
        color: #fff;
        width: fit-content;
    }

    .message-client {
        margin-right: auto;
        margin-left: 0;
        border-bottom-left-radius: 2px;
        border-bottom-right-radius: 12px;
    }
    .msg-bg {
        background: #494949;
    }
    .transparent-bg {
        background:rgba(73, 73, 73, 0) !important;
    }
    .message-admin {
        background: #092CA3;
        margin-left: auto;
        margin-right: 0;
        border-bottom-right-radius: 2px;
        border-bottom-left-radius: 12px;
    }

    /* Hover effect for messages */
    .message-client:hover, .message-admin:hover {
        filter: brightness(1.1);
    }

    /* Unread Indicator */
    .message-admin:not(.readed)::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -8px;
        width: 8px;
        height: 8px;
        /* background: #F8994F; */
        border-radius: 50%;
        transform: translateY(-50%);
    }

    /* Chat Input Area */
    .chat-input {
        display: flex;
        align-items: center;
        padding: 10px;
        background: transparent;
        border-top: none;
    }

    /* Wrapper for the input field and its icons */
    .chat-input-wrapper {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        background: #1B2A47;
        border-radius: 25px;
        padding: 0 10px;
        margin-right: 10px;
    }

    .chat-input-wrapper img.photo-icon {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    .chat-input input {
        flex: 1;
        padding: 10px 0;
        border: none;
        background: transparent;
        color: white;
        outline: none;
        font-size: 0.9rem;
    }

    .chat-input input::placeholder {
        color: #A0AEC0;
    }

    .chat-input input:focus {
        background: transparent;
    }

    /* Icons inside the input field (paperclip and mic) */
    .chat-input-icons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-input-icons i {
        color: #A0AEC0;
        font-size: 20px;
        cursor: pointer;
    }

    .chat-input-icons i:hover {
        color: #fff;
    }

    /* Send button */
    .chat-input button {
        background: #1b2a4700;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .chat-input button img {
        width: 35px;
        height: 35px;
    }

    .chat-input button:hover {
        background: #2A3F5F;
    }
    #chat-toggle-icon {
        max-width: 75% !important;
    }
      /* Styles for the Tunisian phone input group */
    .phone-input-group {
        display: flex;
        align-items: center;
    }

    .phone-input-group .phone-prefix {
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #ced4da;
        text-align: center;
        background-color: #343a40; /* Dark background for the prefix */
        border: 1px solid #495057;
        border-right: none; /* Remove border between prefix and input */
        border-radius: 0.375rem 0 0 0.375rem; /* Rounded corners on the left */
    }

    .phone-input-group .form-control {
        border-radius: 0 0.375rem 0.375rem 0; /* Adjust input corners */
        background-color: #212529; /* Match your dark theme */
        color: white;
        border: 1px solid #495057;
    }

    .phone-input-group .form-control:focus {
        background-color: #212529;
        color: white;
        border-color: #F8994F; /* Highlight with your brand color on focus */
        box-shadow: none;
    }
</style>

@yield('css')

