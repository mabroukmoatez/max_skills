<!-- Menu Navigation starts -->
<nav id="sidebar-nav" style="height: 100vh; display: flex; flex-direction: column;">

    <div class="app-logo">
        <div class="row col-lg-12">
            <!-- Logo div -->
            <div id="logo" class="col-lg-12 d-flex align-items-start">
                <!-- Icon -->
                <a href="#" class="d-block head-icon me-2" role="button" data-bs-toggle="offcanvas"
                   data-bs-target="#profilecanvasRight" aria-controls="profilecanvasRight">
                    <img src="{{ Auth::user()->path_photo ?? asset('../assets/images/ai_avtar/2.jpg')}} " alt="avtar" class="h-40 w-40" style="border-radius:50%;">
                </a>

                <!-- User Info -->
                <div id="info" class="sidebar-user-info">
                    <span class="text-bold" style="color:#fff;font-size:12px;">
                        <strong>{{ Auth::user()->firstname.' '.Auth::user()->name }}</strong>
                    </span>
                    <br>
                    <span class="text-sm" style="color:#fff;font-size:smaller;">{{ Auth::user()->niveau }}</span>
                </div>
            </div>

            <!-- Info div -->

        </div>

        <!-- Toggle button - 3 line hamburger icon -->
        <button id="sidebar-toggle-btn" class="sidebar-toggle-btn" aria-label="Toggle Sidebar">
            <i class="ti ti-menu-2"></i>
        </button>
    </div>
    
    <div class="app-nav" id="app-simple-bar" style="flex: 1; overflow-y: auto;">
        <ul class="main-nav p-0 mt-2" style="display: flex; flex-direction: column; height: 100%;">
            <li class="menu-title">
                <form class="app-form app-icon-form w-100" id="sidebar-search-form">
                    <div class="position-relative">
                        <input type="search" id="sidebar-course-search" name="search" class="form-control search-filter card-text placeholder-white" placeholder="Chercher un apprenant..."
                           aria-label="Search" style="background-color:#1C1F21 !important;border:0px !important;">
                        <i class="ti ti-search text-white"></i>
                    </div>
                </form>
            </li>
            <li class="no-sub" style="margin-bottom:5px;">
                <a class="" href="{{ route('admin.chats.index') }}" style="font-weight:lighter !important;font-size: 22px !important">
                    <p style="font-weight:500 !important;font-size:22px;">Chat</p>
                    <span class="badge text-bg-danger badge-notification ms-2" id="nb_chat_badge">0</span>
                </a>
                <div class="col-12 d-flex align-items-start" style="color: #fff; margin-left: 3%;">
                    <p style="font-size: 12px;"><strong id="total-messages">0</strong> Messages</p>
                    <p style="font-size: 12px;margin-left: 3%;"><strong id="unread-messages">0</strong> non lus</p>
                </div>
            </li>

            <li class="no-sub" style="margin-bottom:5px;">
                <a class="{{ request()->is('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}" aria-expanded="{{ request()->is('admin/admin')  ? 'true' : 'false' }}" style="font-weight:lighter !important" >
                    <i class="ph-duotone  ph-list-checks"></i>
                    <p style="font-weight:500 !important">Tableau de bord</p>
                </a>
            </li>
            <li class="menu-title-small"> <span>Formations</span></li>
            @if(Auth::user()->role === 'admin')
            <li>
                    <a class="" data-bs-toggle="collapse" href="#cours" aria-expanded="{{ request()->is('admin/ajout_cour') || request()->is('admin/cours') ? 'true' : 'false' }}" style="font-weight:lighter !important">
                    <i class="ph-duotone  ph-file-text"></i>
                    <p style="font-weight:500 !important">Cours</p>
                </a>
                <ul class="collapse {{ request()->is('admin/ajout_cour') || request()->is('admin/cours') ? 'show' : '' }}" id="cours">
                    <li class="{{ request()->is('admin/cours') ? 'active' : '' }}"><a href="{{ route('admin.cours') }}">Cours ajoutés</a></li>
                    <li class="{{ request()->is('admin/ajout_cour') ? 'active' : '' }}"><a href="{{ route('admin.ajout_cour') }}">Créer un nouveau cour</a></li>
                </ul>
            </li>
            @endif
            <li>
            @if(Auth::user()->role === 'admin')
                <a class="" data-bs-toggle="collapse" href="#clients" aria-expanded="{{ request()->is('admin/clients') || request()->is('admin/client_ajout') ? 'true' : 'false' }}" style="font-weight:lighter !important">
            @elseif(Auth::user()->role === 'agent')
                <a class="" data-bs-toggle="collapse" href="#clients" aria-expanded="{{ request()->is('agent/clients') || request()->is('agent/client_ajout') ? 'true' : 'false' }}" style="font-weight:lighter !important">
            @endif
                    
                    <i class="ph ph-identification-card"></i>
                    <p style="font-weight:500 !important">Apprenants</p>
                </a>
                @if(Auth::user()->role === 'admin')
                <ul class="collapse {{ request()->is('admin/clients') || request()->is('admin/client_ajout') ? 'show' : '' }}" id="clients">
                    <li class="{{ request()->is('admin/clients') ? 'active' : '' }}">
                        <a href="{{ route('admin.clients') }}">Apprenants inscrits</a>
                         <span id="nb_client_badge" class="badge text-bg-warning badge-notification ms-2">{{ $nb_client }}</span>
                    </li>
                    <li><a href="{{ route('admin.clients') }}?openModal=true">Nouveau apprenant</a></li>
                @elseif(Auth::user()->role === 'agent')
                <ul class="collapse {{ request()->is('agent/clients') || request()->is('agent/client_ajout') ? 'show' : '' }}" id="clients">
                    <li class="{{ request()->is('agent/clients') ? 'active' : '' }}">
                        <a href="{{ route('agent.clients') }}">Apprenants inscrits</a>
                         <span id="nb_client_badge" class="badge text-bg-warning badge-notification ms-2">{{ $nb_client }}</span>
                    </li>
                    <li><a href="{{ route('agent.clients') }}?openModal=true">Nouveau apprenant</a></li>
                @endif
                       
                       
                </ul>
            </li>
            <li class="no-sub"  style="margin-bottom:5px;">
            @if(Auth::user()->role === 'admin')
                <a class="{{ request()->is('admin.chats.index') ? 'active' : '' }}" href="{{ route('admin.chats.index') }}" aria-expanded="{{ request()->is('admin/chats')  ? 'true' : 'false' }}" style="font-weight:lighter !important">
            @elseif(Auth::user()->role === 'agent')
                <a class="{{ request()->is('agent.chats.index') ? 'active' : '' }}" href="{{ route('agent.chats.index') }}" aria-expanded="{{ request()->is('admin/chats')  ? 'true' : 'false' }}" style="font-weight:lighter !important">

            @endif
                    <i class="ph ph-chat-centered-dots"></i> <p style="font-weight:500 !important">Live Chat</p>
                    <span class="badge text-bg-danger badge-notification ms-2"><strong>live</strong></span>
                </a>
            </li>
             @if(Auth::user()->role === 'admin')
                <li class="menu-title-small"> <span>Paramètres</span></li>
                <li class="no-sub">
                    <a class="" style="font-weight:500 !important">
                        <img src="{{asset('../assets/icon/coppon.png')}}" class="b-r-10 h-20 w-20 me-sm-1"> Code Promo
                    </a>
                </li>
                <li class="no-sub">
                    <a class="" style="font-weight:500 !important">
                        <img src="{{asset('../assets/icon/stat.png')}}" class="b-r-10 h-20 w-20 me-sm-1">  Statistiques
                    </a>
                </li>
                <li class="no-sub">
                    <a href="{{ route('admin.users') }}" style="font-weight:500 !important">
                        <img src="{{asset('../assets/icon/users.png')}}" class="b-r-10 h-20 w-20 me-sm-1">  Utilisateurs
                    </a>
                </li>
            @endif
            <li style="position:fixed;bottom: 20px; background-color: inherit;">
                <div class="d-flex justify-content-between align-items-center">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="d-flex align-items-center" href="#" id="logout-link">
                        <i class="ph-duotone ph-sign-out me-2"></i> 
                        Déconnexion
                    </a>
                    <p class="m-0" style="color:#878787;margin-left: 5rem !important;">Version 1.0</p> 
                </div>
            </li>
        </ul>
    </div>

    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>
<script>
    document.getElementById('logout-link').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('logout-form').submit();
    });

    document.addEventListener("DOMContentLoaded", function () {
        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar-nav');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const appWrapper = document.querySelector('.app-wrapper');
        let isHovering = false;

        // Check if there's a saved state in localStorage
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        if (sidebarState === 'true') {
            sidebar.classList.add('semi-nav');
        }

        // Toggle sidebar on button click (permanent toggle)
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            sidebar.classList.toggle('semi-nav');

            // Save state
            if (sidebar.classList.contains('semi-nav')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });

        // Hover functionality for collapsed sidebar
        sidebar.addEventListener('mouseenter', function() {
            if (sidebar.classList.contains('semi-nav')) {
                isHovering = true;
                sidebar.classList.add('hover-expanded');
            }
        });

        sidebar.addEventListener('mouseleave', function() {
            isHovering = false;
            sidebar.classList.remove('hover-expanded');
        });

        function fetchChatCounts() {
            fetch('/admin/get-chat-count', {
                method: 'GET',
                credentials: 'include' // Include cookies and session data
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {

                document.getElementById('nb_chat_badge').textContent = data.total_chat;
                document.getElementById('total-messages').textContent = data.total_messages;
                document.getElementById('unread-messages').textContent = data.unread_messages;
            })
            .catch(error => console.error('Error fetching chat counts:', error));
        }

        fetchChatCounts();

        setInterval(fetchChatCounts, 5000);

        const searchForm = document.getElementById('sidebar-search-form');
        const searchInput = document.getElementById('sidebar-course-search');

        searchForm.addEventListener('submit', function(event) {
            // Empêche le formulaire de se soumettre de manière classique
            event.preventDefault();

            const searchValue = searchInput.value.trim();

            if (searchValue) {
                // Construit l'URL de la page des clients avec le paramètre de recherche
                // Assurez-vous que la route 'admin.clients' est correcte
                const url = `{{ route('admin.clients') }}?search=${encodeURIComponent(searchValue)}`;

                // Redirige l'utilisateur vers la nouvelle URL
                window.location.href = url;
            }
        });

        // Optionnel : Vider le placeholder au focus
        searchInput.addEventListener('focus', function() {
            this.placeholder = '';
        });

        searchInput.addEventListener('blur', function() {
            this.placeholder = 'Chercher un apprenant...';
        });
    });
</script>
<!-- Menu Navigation ends -->
