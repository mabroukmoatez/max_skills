<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="{{ asset('assets/images/background/logo.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('assets/images/background/logo.png') }}" type="image/x-icon">
<title>@yield('title') | MaxSkills</title>
<script src="https://cdn.ably.com/lib/ably.min-2.js"></script>
<!-- meta and title end-->
@livewireStyles
    <!-- css start-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.css') }}" >
@yield('css')
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
<!-- latest jquery-->
<script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>

<!-- Bootstrap js-->
<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>


<!-- FilePond Core + Plugins -->
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
</script>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
@yield('script')

<script>
   function redirectHome() {
        window.location.href = 'https://maxskills.tn/formation/cour/9f705dc0-61fc-4b19-9b10-9fe097f618de';
    }
</script>
<!-- scripts end-->
@livewireScripts

</html>
