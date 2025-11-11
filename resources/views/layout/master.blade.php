<!DOCTYPE html>
<html lang="en">

<head>
    <!-- All meta and title start-->
@include('layout.head')
<!-- meta and title end-->
@livewireStyles
    <!-- css start-->
@include('layout.css')
<!-- css end-->
</head>

<body>
<!-- Loader start-->
<div class="app-wrapper">
    <div class="loader-wrapper">
        <div class="loader_16"></div>
    </div>
    <!-- Loader end-->

    <!-- Menu Navigation start -->
    @include('layout.sidebar')
    <!-- Menu Navigation end -->


    <div class="app-content">
        <!-- Main Section start -->
        <main>
            {{-- main body content --}}
            @yield('main-content')
        </main>
        <!-- Main Section end -->
    </div>

    <!-- tap on top -->
    <div class="go-top">
      <span class="progress-value">
        <i class="ti ti-arrow-up"></i>
      </span>
    </div>

    <!-- Footer Section start -->
     @include('layout.footer')
    <!-- Footer Section end -->
</div>

</body>

<!-- scripts start-->
@include('layout.script')
<!-- scripts end-->
@livewireScripts
<script>
    Livewire.on('post-created', (event) => {
        setTimeout(() => {
            console.log('Dispatching clear-flash-client event');
            Livewire.dispatch('clear-flash-client');
        }, 500);
    });

    Livewire.on('success', (event) => {
        setTimeout(() => {
            console.log('Dispatching clear-flash-client event 2');
            Livewire.dispatch('clear-file-preview'); // Fixed: Use Livewire.dispatch instead of $this->dispatch
        }, 500);
    });

    Livewire.on('modal-opened', () => {
        const clientsElement = document.querySelector('#clients');
        if (clientsElement) {
            const firstLi = clientsElement.querySelector('li:nth-child(1)');
            const secondLi = clientsElement.querySelector('li:nth-child(2)');
            if (firstLi) firstLi.classList.remove('active');
            if (secondLi) secondLi.classList.add('active');
        }
    });

    Livewire.on('modal-closed', () => {
        const clientsElement = document.querySelector('#clients');
        if (clientsElement) {
            const firstLi = clientsElement.querySelector('li:nth-child(1)');
            const secondLi = clientsElement.querySelector('li:nth-child(2)');
            if (secondLi) secondLi.classList.remove('active');
            if (firstLi) firstLi.classList.add('active');
        }
    });

    Livewire.on('nb-client-updated', (event) => {
        const nbClient = event.nb_client;
        const badgeElement = document.querySelector('#nb_client_badge');
        if (badgeElement) {
            badgeElement.textContent = nbClient;
        }
    });

    Livewire.on('nb-chat-updated', (event) => {
        const nbChat = event.nb_client;
        const badgeElement = document.querySelector('#nb_chat_badge');
        if (badgeElement) {
            badgeElement.textContent = nbChat;
        }
    });

    Livewire.on('confirmDelete', (userId) => {
        if (confirm('Are you sure you want to delete this agent?')) {
             Livewire.dispatch('deleteUser', userId);
        }
    });
 
</script>
</html>
