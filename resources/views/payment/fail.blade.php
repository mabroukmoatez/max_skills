<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Échec du paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f3f4f6; /* Light gray background */
        }
        .card {
            background-color: #ffffff;
            border-radius: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 28rem; /* Equivalent to md:max-w-md */
        }
        .icon-circle {
            border-radius: 50%;
            width: 5rem;
            height: 5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 0 auto;
        }
        /* Style for the actual image inside the circle */
        .icon-circle img {
            max-width: 100%; /* Adjust size of the image within the circle */
            max-height: 100%; /* Adjust size of the image within the circle */
            object-fit: contain;
        }
        .btn-fail {
            border-radius : 0.6rem !important;
        }
    </style>
</head>
<body>
    <div class="card p-8 text-center">
        <div class="icon-circle">
            <img src="{{ asset('img/fail.png') }}" alt="Failure Icon">
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Échec du paiement</h2>
        <p class="text-gray-600 mb-8" style="padding-left: 1rem;" >
            Merci de réessayer le paiement afin de finaliser votre inscription.
        </p>
        <a href="{{ url('/formation/payment/50') }}" class="btn-fail inline-block text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200" style="background-color:#000;">
            Réessayer le paiement
        </a>
        {{-- Optionally display payment details for debugging in development --}}
       
    </div>
</body>
</html>