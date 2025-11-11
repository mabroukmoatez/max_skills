<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/background/logo.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('assets/images/background/logo.png') }}" type="image/x-icon">
<title>@yield('title') | MaxSkills</title>
<script src="https://cdn.ably.com/lib/ably.min-2.js"></script>
<style>
    .profile-pic-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .profile-pic-container::after {
        content: 'Changer la photo';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        pointer-events: none;
    }

    .profile-pic-container:hover::after {
        opacity: 1;
    }

    .profile-pic {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }
    .modal {
        --bs-modal-margin: 1.75rem;
        --bs-modal-box-shadow: 0 0.5rem 1rem rgba(var(--bs-body-color-rgb), 0.15);
    }
</style>
