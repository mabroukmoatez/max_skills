<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- All meta and title start-->
@include('client.layout.head')
<!-- meta and title end-->
@livewireStyles
    <!-- css start-->
@include('client.layout.css')
<!-- css end-->
</head>
 
<body>
<!-- Loader start-->
<div class="app-wrapper">
    <div class="loader-wrapper">
        <div class="loader_16"></div>
    </div>
 
    <!-- Header Section end -->
        <!-- Main Section start -->
        <main>
            {{-- main body content --}}
            @yield('main-content')
        </main>
</div>

</body>

<!-- scripts start-->
@include('client.layout.script')

<script>
   function redirectHome() {
        window.location.href = 'https://maxskills.tn/formation/cour/9f705dc0-61fc-4b19-9b10-9fe097f618de';
    }
</script>
<!-- scripts end-->
@livewireScripts

</html>
