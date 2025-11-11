<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f9fafb; /* Light gray background */
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        .card {
            background-color: #ffffff;
            border-radius: 1.8rem; /* 8px */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 31rem; /* Equivalent to md:max-w-md */
            position: relative; /* For the close button */
        }
        .icon-circle {
            border-radius: 50%;
            width: 5rem; /* 80px */
            height: 5rem; /* 80px */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 0 auto; /* Center and provide spacing */
        }
        /* Style for the actual image inside the circle */
        .icon-circle img {
            max-width: 100%; /* Adjust size of the image within the circle */
            max-height: 100%; /* Adjust size of the image within the circle */
            object-fit: contain;
        }
        /* Style for the close button, similar to the image */
        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #9ca3af; /* Gray color */
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }
        .close-button:hover {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="card p-8 text-center">
        <button class="close-button" onclick="window.location.href='{{ url('/login') }}'">
            &times;
        </button>
        <div class="icon-circle">
            <img src="{{ asset('img/success.png') }}" alt="Success Icon">
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">L'opération a été effectuée avec succès</h2>
         <p class="mb-8">
         Votre mot de passe vous sera envoyé par email dans quelques instants.
         </p>
           
        <a href="{{ url('/login') }}" class="inline-block bg-black text-white font-bold py-2 px-6 hover:bg-gray-800 transition duration-200" style="border-radius:0.5rem;">
            Connexion
        </a>

        {{-- Optionally display payment details for debugging in development --}}
      
    </div>
</body>
</html>