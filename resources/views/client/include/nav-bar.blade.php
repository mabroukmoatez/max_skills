<div class="nav-row">
    <!-- Logo on the left -->
    <img src="{{ asset('client/images/logo.png') }}" alt="Logo" onclick="redirectHome()">

    <!-- Notification and Profile Icons on the right -->
    <div class="dropdown-container">
        <!-- Notification Icon with Dropdown -->
        <div class="notification-dropdown">
            <div class="notification-icon" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-bell"></i>
                @php
                    $unreadCount = \App\Models\Notification::where('reciver_id', auth()->id())
                                    ->where('status', 0)
                                    ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
            </div>
            <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu" aria-labelledby="notificationDropdown">
                <!-- Notification Header -->
                <li class="notification-header">
                    <h6>Notification</h6>
                    <i class="fas fa-chevron-up close-icon"></i>
                </li>
                <!-- Notifications will be loaded via JavaScript -->
                <li class="text-center p-3">
                    <div class="spinner-border spinner-border-sm text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Profile Icon with Dropdown -->
        <div class="dropdown">
            <div class="profile-icon" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span>
                    <img src="{{ asset(auth()->user()->path_photo ?? 'client/images/client_profil.png') }}" style="border-radius: 15% !important;">
                </span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="profileDropdown" style="margin:10px !important;width:180px !important;">
                <li>
                    <a class="dropdown-item text-start" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-user icon" style="margin-right:7px;"></i> Profil ({{ auth()->user()->name }})
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-start" href="#">
                        <i class="fas fa-circle-info" style="margin-right:7px;"></i> Centre d'aide
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li class="text-center">
                    <a class="dropdown-item-last" href="{{ route('logout_get') }}">
                        Se déconnecter
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
