<!-- Animation css -->
<link rel="stylesheet" href="{{ asset('assets/vendor/animation/animate.min.css') }}" >

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400..900&display=swap" rel="stylesheet">

<!-- Weather icon css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/weather/weather-icons.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/weather/weather-icons-wind.css') }}">

<!--Flag Icon css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/flag-icons-master/flag-icon.css') }}">

<!-- Tabler icons-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">

<!-- Prism css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/prism/prism.min.css') }}">

<!-- Bootstrap css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">

<!-- Simplebar css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/simplebar/simplebar.css') }}">

<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.css') }}" >

<style>
    ::placeholder {
  color: white !important;
  opacity: 1 !important;
}

/* Target specific inputs if needed */
.input-white-placeholder::placeholder {
  color: white !important;
  opacity: 1 !important;
}
/* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0); /* Semi-transparent white background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000; /* Ensure it's above other content */
    }

    /* Loading Spinner */
    .loading-spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #48bece; /* Blue */
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    .loading-overlay p {
        margin-top: 10px;
        font-size: 16px;
        color: #48bece; /* Blue color */
    }
    
    /* Spin Animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    /* Change text color to white */
    .search-filter {
    color: white !important; /* Ensures text color is white */
}

</style>
@yield('css')

@vite(['public/assets/scss/style.scss'])
