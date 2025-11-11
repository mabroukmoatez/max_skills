<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://maxskills.tn/assets/images/background/logo.png" rel="icon" media="(prefers-color-scheme: light)">
    <link href="https://maxskills.tn/assets/images/background/logo.png" rel="icon" media="(prefers-color-scheme: dark)">
    <link rel="apple-touch-icon" href="https://maxskills.tn/assets/images/background/logo.png">
    <style>
        body {
            font-family: sans-serif;
        }
        @media (min-width: 994px) {
            .right-half {
                padding-right: 15%;
            }
        }
        .left-half {
            background-color: #00000E;
            padding:8rem 8rem 8rem 12rem;
        }
        .right-half {
            background-color: #ffffff;
        }
        /* Custom styles for dark input, if not handled by framework */
        .dark-input {
            background-color: #333;
            color: white;
            border: 1px solid #555;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }
        .dark-input::placeholder {
            color: #aaa;
        }

        /* Custom Radio Button Styles */
        .custom-radio-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 0.5rem;
            border: 0.1px solid #e2e8f0;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        .custom-radio-container:hover {
            background-color: #f7fafc;
        }

        .custom-radio-input {
            display: none;
        }

        .custom-radio-button {
            width: 0.75rem;
            height: 0.75rem;
            border: 1px solid #000;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            margin-right: 0.5rem;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .custom-radio-input:checked + .custom-radio-button::after {
            content: '';
            width: 0.75rem;
            height: 0.75rem;
            background-color: #000;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            font-size: 17px;
            color: #4a5568;
            flex-grow: 1;
            padding:3px;
        }

        .custom-radio-icon {
            width: 24px;
            height: 24px;
            margin-right: 8px;
            object-fit:cover;
        }
        .rounded {
            border-radius:8px !important;
        }
        .not-rounded {
            border-radius:0px !important;
        }
        .rounded-top {
            border-radius:8px 8px 0px 0px !important;
        }
        .rounded-bottom {
            border-radius:0px 0px 8px 8px !important;
        }
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .phone-input-wrapper {
            min-height: 42px;
            overflow: hidden;
            padding-right: calc(60px + 0.5rem);
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border-radius: 0.25rem;
        }

        .phone-flag {
            width: 24px;
            height: 24px;
            margin-left: 0.75rem;
            margin-right: 0.25rem;
            flex-shrink: 0;
            object-fit: contain;
        }

        .country-code-display {
            white-space: nowrap;
            margin-right: 0.5rem;
        }

        .phone-input-wrapper input[type="tel"] {
            border: none;
            padding-left: 0;
            padding-right: 0;
        }

        .obligatoire-badge {
            white-space: nowrap;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        .icon-formation {
            width: 80px;
            height: 80px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .text-xl-18 {
            font-size: 18px;
        }
        .text-red{
            color:#b91c1c96;
        }
        .border-grey-color {
            border-color:rgba(55,65,81,0.4);
        }

        /* New CSS to control visibility of main sections within right-half */
        .right-half > *:not(.payment-method-sections) {
            display: block;
        }
        .payment-method-sections > div {
            display: none;
        }
        #initial-right-half-content {
            display: block;
        }

        /* Custom File Upload Styles */
        .custom-file-upload {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background-color: #f9fafb;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }

        .custom-file-upload:hover {
            border-color: #9ca3af;
            background-color: #f3f4f6;
        }

        .custom-file-upload.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            opacity: 0.6;
        }

        .upload-text {
            color: #374151;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: #6b7280;
            font-size: 14px;
        }

        .file-preview-container {
            margin-top: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background-color: #f8f9ff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            background-color: #f1f5f9;
        }

        .file-icon {
            width: 20px;
            height: 20px;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .file-info {
            flex-grow: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }

        .file-size {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 0.5rem;
        }

        .progress-container {
            width: 100%;
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .progress-bar {
            height: 100%;
            background-color: #1f2937;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            color: #6b7280;
            text-align: right;
        }

        .file-actions {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }

        .remove-file-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #f3f4f6;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .remove-file-btn:hover {
            background-color: #ef4444;
            color: white;
        }

        .remove-file-btn svg {
            width: 12px;
            height: 12px;
        }

        .hidden-file-input {
            display: none;
        }

        /* Error states */
        .file-item.error {
            background-color: #fef2f2;
            border-color: #fecaca;
        }

        .file-item.error .file-name {
            color: #dc2626;
        }

        .file-item.error .progress-bar {
            background-color: #dc2626;
        }

        /* Success states */
        .file-item.success .progress-bar {
            background-color: #059669;
        }

        .file-item.success .remove-file-btn {
            background-color: #d1fae5;
            color: #059669;
        }

    /* --- Responsive Styles for Mobile --- */
    @media (max-width: 768px) {
        .left-half, .right-half {
            /* Remove fixed padding and set to a more mobile-friendly value */
            padding: 2rem; /* 32px padding on all sides */
        }

        .left-half {
            /* Ensure the top (black) section has rounded corners only at the top */
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .right-half {
            /* Ensure the bottom (white) section has rounded corners only at the bottom */
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        /* Adjust the layout of the course info in the black section */
        .course-info-mobile {
            flex-direction: column; /* Stack image and text vertically */
            align-items: center; /* Center items */
            text-align: center; /* Center text */
        }

        .icon-formation {
            display:none;
            margin-right: 0; /* Remove right margin */
            margin-bottom: 1rem; /* Add bottom margin for spacing */
        }

        .course-text-mobile {
            max-width: 100%; /* Allow text to take full width */
        }

        /* Center the subtotal/total section */
        .totals-section-mobile {
            padding-left: 0; /* Remove the desktop-specific left padding */
        }
        #amount-div{
            padding-left: 0 !important;
        }
    }
</style>

</head>
<body>
    <div class="min-h-screen flex flex-col md:flex-row">
        <div class="left-half w-full md:w-1/2 flex flex-col justify-between text-white">
            <div>
                <div class="text-3xl font-bold mb-8">{{ $cour->price_init ?? '0.00' }} TND</div>

                <div class="flex justify-between items-center mb-6" style="display: flex;flex-direction: row;flex-wrap: nowrap; align-content: space-between;justify-content: space-between;align-items: flex-start;">
                    <div>
                        <div class="flex items-start">
                            <img src="{{ asset('assets/icon/photoshop.png') }}" alt="Photoshop Icon" class="icon-formation mr-4">
                            <div style=" max-width: 65%; ">
                                <div class="text-lg font-semibold">{{ $cour->title ?? 'Course Title' }}</div>
                                <div class="text-sm text-gray-400">Abonnement Annuel</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-lg font-semibold">{{ $cour->price_init ?? '0.00' }}</div>
                </div>
                <div style="padding-left:20%;" id="amount-div">
                    <div class="flex mb-4 border-t border-grey-color pt-6 justify-between items-center">
                        <div class="text-small font-bold">Soustotale</div>
                        <div class="text-small font-bold">{{ number_format(($cour->price_init ?? 0) , 2) }} TND</div>
                    </div>
                    <div class="mb-6 flex justify-between items-center">
                        <input type="text" placeholder="Ajouter un code promo" class="dark-input p-2 rounded" style="width:50%;">
                    </div>

                    <div class="border-t border-grey-color pt-6 flex justify-between items-center">
                        <div class="text-xl-18 font-bold">Total dû aujourd'hui</div>
                        <div class="text-xl-18 font-bold">{{ number_format(($cour->price_init ?? 0) , 2) }} TND</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-half w-full md:w-1/2 pr-50 px-14 py-20 flex flex-col">

            <div id="initial-right-half-content">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800" style="font-size:18px !important;color:rgba(26, 26, 26, 0.7)">Contact information</h2>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="xxxxxxxxx@gmail.com" value="{{ Auth::user()->email ?? '' }}">
                </div>

                <h2 class="text-2xl font-semibold mb-6 text-gray-800" style="font-size:18px !important;color:rgba(26, 26, 26, 0.7)">Méthode de paiement</h2>
                <div class="mb-6">
                    <label for="konnect" class="custom-radio-container rounded-top">
                        <input type="radio" id="konnect" name="payment_method" value="konnect" class="custom-radio-input" checked>
                        <span class="custom-radio-button"></span>
                        <span class="custom-radio-label">
                            <img src="{{ asset('assets/icon/konnect.png') }}" alt="Konnect Icon" class="custom-radio-icon">
                            Paiement en ligne
                        </span>
                    </label>

                    <label for="virement_bancaire" class="custom-radio-container not-rounded">
                        <input type="radio" id="virement_bancaire" name="payment_method" value="virement_bancaire" class="custom-radio-input">
                        <span class="custom-radio-button"></span>
                        <span class="custom-radio-label">
                            <img src="{{ asset('assets/icon/attijari.png') }}" alt="Virement Bancaire Icon" class="custom-radio-icon">
                            Virement Bancaire
                        </span>
                    </label>
                    <label for="d17" class="custom-radio-container not-rounded">
                        <input type="radio" id="d17" name="payment_method" value="d17" class="custom-radio-input">
                        <span class="custom-radio-button"></span>
                        <span class="custom-radio-label">
                            <img src="{{ asset('assets/icon/d17.png') }}" alt="Virement Bancaire Icon" class="custom-radio-icon">
                            DigiPostBank D17 / Mondat
                        </span>
                    </label>
                    <label for="espece" class="custom-radio-container rounded-bottom">
                        <input type="radio" id="espece" name="payment_method" value="espece" class="custom-radio-input">
                        <span class="custom-radio-button"></span>
                        <span class="custom-radio-label">
                            <img src="{{ asset('assets/icon/1dt.png') }}" alt="Espèce Icon" class="custom-radio-icon">
                            Espèce
                        </span>
                        </label>
                </div>

                <div class="mb-6">
                    <label for="phone" class="sr-only">Téléphone</label>

                    <div class="phone-input-wrapper relative flex items-center shadow appearance-none border rounded w-full">
                        <img src="{{ asset('assets/icon/tunisia-flag.png') }}" alt="Tunisian Flag" class="phone-flag">
                        <span class="country-code-display text-gray-700 ml-1">(+216)</span>

                        <input type="tel" id="phone" name="phone"
                            class="flex-grow py-2 px-3 text-gray-700 leading-tight focus:outline-none bg-transparent"
                            placeholder="xx xxx xxx">

                        <span class="obligatoire-badge absolute right-2 py-1 px-3 rounded-full text-xs font-semibold text-red bg-red-100">
                            Obligatoire
                        </span>
                    </div>
                </div>
                <div class="mb-2 flex items-center">
                    <input type="checkbox" id="accept_terms" class="mr-2">
                    <label for="accept_terms" class="text-gray-700 text-sm">
                        J'accepte les conditions d'utilisation de MaxSkills.tn et politique de confidentialité
                    </label>
                </div>
                <div class="full-w">
                   <button id="proceedToPaymentBtn"
                        style="background-color:#FF9B4C;color:#000000;width:-webkit-fill-available"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded focus:outline-none focus:shadow-outline mt-6"
                        data-price="{{ $cour->price_init ?? 0 }}"
                        data-course-id="{{ $cour->id ?? '' }}"
                        data-first-name="{{ Auth::user()->first_name ?? '' }}"
                        data-last-name="{{ Auth::user()->last_name ?? '' }}"
                        data-email="{{ Auth::user()->email ?? '' }}"
                        data-phone-number="{{ Auth::user()->phone ?? '' }}">
                        S'inscrire
                    </button>
                     <button id="proceedToPaymentShowDivBtn"
                        style="background-color:#FF9B4C;color:#000000;width:-webkit-fill-available"
                        class="bg-orange-500 hidden hover:bg-orange-700 text-white font-medium py-3 px-4 rounded focus:outline-none focus:shadow-outline mt-6"
                       >
                        Confirm
                    </button>
                </div>
            </div>

            <div id="payment-details-sections" class="flex flex-col flex-grow hidden">
                <div id="payment-details-konnect-only" >
                </div>

                <div id="payment-details-virement" >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Téléchargez le reçu bancaire</h3>
                        <button class="return-to-main-btn text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                    </div>
                    <div class="mb-4">
                        <label for="bank_holder_name" class="block text-gray-700 text-sm font-bold mb-2">Nom de titulaire</label>
                        <input type="text" id="bank_holder_name" name="bank_holder_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="Société Maxskills Pro" readonly>
                    </div>
                    <div class="mb-4">
                        <label for="rib" class="block text-gray-700 text-sm font-bold mb-2">Rib bancaire</label>
                        <div class="relative">
                            <input type="text" id="rib" name="rib" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="4950 5410 3654 6510 6985" readonly>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" onclick="copyToClipboard('rib')">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="bank_name" class="block text-gray-700 text-sm font-bold mb-2">Banque</label>
                        <div class="flex items-center border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <input type="text" id="bank_name" name="bank_name" class="appearance-none border-none w-full text-gray-700 leading-tight focus:outline-none" value="Attijari Bank" readonly>
                            <img src="{{ asset('assets/icon/attijari.png') }}" alt="Attijari Bank Logo" class="h-6 ml-2">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="proof_of_payment" class="block text-gray-700 text-sm font-bold mb-2">Preuve de paiement</label>
                        
                        <!-- Custom File Upload Component -->
                        <div class="custom-file-upload" id="virement-upload-area">
                            <input type="file" id="proof_of_payment" name="proof_of_payment" class="hidden-file-input" multiple accept="image/*,.pdf">
                            <div class="upload-content">
                                <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="upload-text">Cliquez pour télécharger ou glisser-déposer</div>
                                <div class="upload-subtext">Formats JPEG, PNG, GIF, WebP et PDF, jusqu'à 20 Mo</div>
                            </div>
                        </div>
                        <div id="virement-file-preview" class="file-preview-container"></div>
                    </div>
                    <button id="confirm-virement-btn"
                        style="background-color:#FF9B4C;color:#000000;width:-webkit-fill-available"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded focus:outline-none focus:shadow-outline mt-6">
                        Confirmer
                    </button>
                </div>

                <div id="payment-details-d17" >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Téléchargez le reçu D17 ou bien mondat</h3>
                        <button class="return-to-main-btn text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                    </div>
                    <div class="mb-4">
                        <label for="d17_holder_name" class="block text-gray-700 text-sm font-bold mb-2">Nom de titulaire</label>
                        <input type="text" id="d17_holder_name" name="d17_holder_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly value="Hannachi Mohamed Fatah">
                    </div>
                    <div class="mb-4">
                        <label for="d17_phone_number" class="block text-gray-700 text-sm font-bold mb-2">Numéro téléphone D17</label>
                        <div class="relative">
                            <input type="tel" id="d17_phone_number" name="d17_phone_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly value="29499214">
                            <img src="{{ asset('assets/icon/d17.png') }}" alt="D17 Logo" class="absolute inset-y-0 right-0 pr-3 flex items-center h-6">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="d17_rib_postal" class="block text-gray-700 text-sm font-bold mb-2">Rib postal</label>
                        <div class="relative">
                            <input type="text" id="d17_rib_postal" name="d17_rib_postal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly value="4950 5410 3654 6510 6985">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" onclick="copyToClipboard('d17_rib_postal')">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="proof_of_payment_d17" class="block text-gray-700 text-sm font-bold mb-2">Preuve de paiement</label>
                        
                        <!-- Custom File Upload Component for D17 -->
                        <div class="custom-file-upload" id="d17-upload-area">
                            <input type="file" id="proof_of_payment_d17" name="proof_of_payment_d17" class="hidden-file-input" multiple accept="image/*,.pdf">
                            <div class="upload-content">
                                <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="upload-text">Cliquez pour télécharger ou glisser-déposer</div>
                                <div class="upload-subtext">Formats JPEG, PNG, GIF, WebP et PDF, jusqu'à 2 Mo</div>
                            </div>
                        </div>
                        <div id="d17-file-preview" class="file-preview-container"></div>
                    </div>
                    <button id="confirm-d17-btn"
                        style="background-color:#FF9B4C;color:#000000;width:-webkit-fill-available"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded focus:outline-none focus:shadow-outline mt-6">
                        Confirmer
                    </button>
                </div>

                <div id="payment-details-espece" >
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Paiement en Espèce</h3>
                    <p class="text-gray-700 mb-4">Veuillez vous rendre à notre adresse pour effectuer le paiement en espèce.</p>
                    <div class="bg-gray-200 h-64 flex items-center justify-center text-gray-600 rounded">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3289.9525034255125!2d10.173750475801777!3d36.81156386705884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd347be94679af%3A0xed88f2414425628a!2sChedli%20Kallela%2C%20Tunis!5e1!3m2!1sfr!2stn!4v1750885858321!5m2!1sfr!2stn"style="width:100%;height:100%;" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <button id="confirm-espece-btn"
                        style="background-color:#FF9B4C;color:#000000;width:-webkit-fill-available"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded focus:outline-none focus:shadow-outline mt-6">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File upload functionality
        class CustomFileUpload {
            constructor(uploadAreaId, fileInputId, previewContainerId) {
                this.uploadArea = document.getElementById(uploadAreaId);
                this.fileInput = document.getElementById(fileInputId);
                this.previewContainer = document.getElementById(previewContainerId);
                this.files = [];
                this.maxFileSize = 40 * 1024 * 1024; // 2MB
                this.allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                
                this.init();
            }

            init() {
                // Click to upload
                this.uploadArea.addEventListener('click', () => {
                    this.fileInput.click();
                });

                // File input change
                this.fileInput.addEventListener('change', (e) => {
                    this.handleFiles(e.target.files);
                });

                // Drag and drop
                this.uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.add('dragover');
                });

                this.uploadArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.remove('dragover');
                });

                this.uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.remove('dragover');
                    this.handleFiles(e.dataTransfer.files);
                });
            }

            handleFiles(fileList) {
                Array.from(fileList).forEach(file => {
                    if (this.validateFile(file)) {
                        this.addFile(file);
                    }
                });
            }

            validateFile(file) {
                if (!this.allowedTypes.includes(file.type)) {
                    alert(`Type de fichier non supporté: ${file.name}`);
                    return false;
                }

                if (file.size > this.maxFileSize) {
                    alert(`Fichier trop volumineux: ${file.name}. Taille maximale: 20MB`);
                    return false;
                }

                return true;
            }

            addFile(file) {
                const fileId = Date.now() + Math.random();
                const fileObj = {
                    id: fileId,
                    file: file,
                    progress: 0,
                    status: 'uploading'
                };

                this.files.push(fileObj);
                this.renderFileItem(fileObj);
                this.simulateUpload(fileObj);
            }

            renderFileItem(fileObj) {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.setAttribute('data-file-id', fileObj.id);

                const fileIcon = this.getFileIcon(fileObj.file.type);
                const fileSize = this.formatFileSize(fileObj.file.size);

                fileItem.innerHTML = `
                    <div class="file-icon">
                        ${fileIcon}
                    </div>
                    <div class="file-info">
                        <div class="file-name">${fileObj.file.name}</div>
                        <div class="file-size">${fileSize}</div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: ${fileObj.progress}%"></div>
                        </div>
                        <div class="progress-text">${fileObj.progress}%</div>
                    </div>
                    <div class="file-actions">
                        <button class="remove-file-btn" onclick="fileUpload${this.uploadArea.id.replace('-', '')}.removeFile(${fileObj.id})">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;

                this.previewContainer.appendChild(fileItem);
            }

            simulateUpload(fileObj) {
                const interval = setInterval(() => {
                    fileObj.progress += Math.random() * 30;
                    if (fileObj.progress >= 100) {
                        fileObj.progress = 100;
                        fileObj.status = 'completed';
                        clearInterval(interval);
                        this.updateFileItem(fileObj);
                    } else {
                        this.updateFileItem(fileObj);
                    }
                }, 200);
            }

            updateFileItem(fileObj) {
                const fileItem = document.querySelector(`[data-file-id="${fileObj.id}"]`);
                if (fileItem) {
                    const progressBar = fileItem.querySelector('.progress-bar');
                    const progressText = fileItem.querySelector('.progress-text');
                    
                    progressBar.style.width = `${fileObj.progress}%`;
                    progressText.textContent = `${Math.round(fileObj.progress)}%`;

                    if (fileObj.status === 'completed') {
                        fileItem.classList.add('success');
                    }
                }
            }

            removeFile(fileId) {
                this.files = this.files.filter(f => f.id !== fileId);
                const fileItem = document.querySelector(`[data-file-id="${fileId}"]`);
                if (fileItem) {
                    fileItem.remove();
                }
            }

            getFileIcon(fileType) {
                if (fileType.startsWith('image/')) {
                    return `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>`;
                } else if (fileType === 'application/pdf') {
                    return `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>`;
                }
                return `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }

        // Initialize file upload components
        let fileUploadvirementuploadarea, fileUploadd17uploadarea;

        // Payment method switching logic
        $(document).ready(function() {
            // Initialize file upload components after DOM is ready
            fileUploadvirementuploadarea = new CustomFileUpload('virement-upload-area', 'proof_of_payment', 'virement-file-preview');
            fileUploadd17uploadarea = new CustomFileUpload('d17-upload-area', 'proof_of_payment_d17', 'd17-file-preview');

            $('input[name="payment_method"]').change(function() {
                const selectedMethod = $(this).val();

                if (selectedMethod === 'konnect') {
                    // Show Konnect's direct payment button
                    $('#proceedToPaymentBtn').show();
                    // Hide the generic 'Confirm' button
                    $('#proceedToPaymentShowDivBtn').hide();
                } else {
                    // Hide Konnect's button
                    $('#proceedToPaymentBtn').hide();
                    // Show the generic 'Confirm' button for other methods
                    $('#proceedToPaymentShowDivBtn').show();
                }
                // Crucially, we do NOT hide/show payment details sections here.
                // That happens only after clicking the 'Confirm' button.
            });

            $('#proceedToPaymentBtn').click(function() {
                // Disable the button and change text to indicate processing
                $(this).text('Processing...').prop('disabled', true);

                // Get contact details from input fields
                var email = $('#email').val();
                var phoneNumber = $('#phone').val(); // Corrected ID to 'phone'
                var termsAccepted = $('#accept_terms').is(':checked');

                // Get other data from the button's data attributes
                var price = $(this).data('price');
                var courseId = $(this).data('course-id');
                var firstName = $(this).data('first-name'); // Using data attributes for these, as per your HTML
                var lastName = $(this).data('last-name');
            
                // Basic validation for Konnect before proceeding
                if (!email || !phoneNumber || !termsAccepted) {
                    let errorMessage = 'Please ensure the following before proceeding with Konnect: \n';
                    if (!email) errorMessage += '- Email is provided.\n';
                    if (!phoneNumber) errorMessage += '- Phone Number is provided.\n';
                    if (!termsAccepted) errorMessage += '- You accept the Terms and Conditions.\n';
                    alert(errorMessage);
                    $(this).text('S\'inscrire').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ route('konnect.initiate') }}", // Ensure this Laravel route is correctly defined
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}", // Laravel CSRF token for security
                        amount: price,
                        course_id: courseId,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        phone_number: phoneNumber
                    },
                    success: function(response) {
                        if (response.success && response.redirect_url) {
                            // If successful, redirect the user to Konnect's payment page
                            window.location.href = response.redirect_url;
                        } else {
                            // Alert if payment initiation failed and re-enable button
                            alert('Payment initiation failed: ' + (response.message || 'Unknown error.'));
                            $('#proceedToPaymentBtn').text('S\'inscrire').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Log detailed error and alert user, then re-enable button
                        console.error("AJAX error: ", status, error, xhr.responseText);
                        alert('An error occurred during payment. Please try again.');
                        $('#proceedToPaymentBtn').text('S\'inscrire').prop('disabled', false);
                    }
                });
            });


            $('#proceedToPaymentShowDivBtn').click(function() {
                // Get the currently selected payment method
                const selectedMethod = $('input[name="payment_method"]:checked').val();

                // Get values for validation
                var email = $('#email').val();
                var phoneNumber = $('#phone').val(); // Corrected ID to 'phone'
                var termsAccepted = $('#accept_terms').is(':checked'); // Ensure checkbox has ID 'accept_terms'

                // Perform validation
                if (!email || !phoneNumber || !termsAccepted) {
                    let errorMessage = 'To proceed, please ensure the following: \n';
                    if (!email) errorMessage += '- Email is provided.\n';
                    if (!phoneNumber) errorMessage += '- Phone Number is provided.\n';
                    if (!termsAccepted) errorMessage += '- You accept the Terms and Conditions.\n';
                    alert(errorMessage);
                    return; // Stop execution if validation fails
                }

                if (selectedMethod && selectedMethod !== 'konnect') {
                    $('#initial-right-half-content').hide();
                    $('#payment-details-sections').show();

                    $('#payment-details-virement').hide();
                    $('#payment-details-d17').hide();
                    $('#payment-details-espece').hide();
                    if(selectedMethod == 'virement_bancaire') {
                        $('#payment-details-virement').show();
                    }
                    $(`#payment-details-${selectedMethod}`).show();
                } else if (!selectedMethod) {
                    alert('Please select a payment method.');
                }
            });

            $('.return-to-main-btn').click(function() {
                // Hide all payment details sections
                $('#payment-details-sections').hide();
                // Show the initial content (contact info and method selection)
                $('#initial-right-half-content').show();
                // Reset button visibility based on currently selected radio (optional, but good practice)
                const currentSelectedMethod = $('input[name="payment_method"]:checked').val();
                if (currentSelectedMethod === 'konnect') {
                    $('#proceedToPaymentBtn').show();
                    $('#proceedToPaymentShowDivBtn').hide();
                } else {
                    $('#proceedToPaymentBtn').hide();
                    $('#proceedToPaymentShowDivBtn').show();
                }
            });


            // --- Helper function for copying text to clipboard ---
            function copyToClipboard(elementId) {
                var copyText = document.getElementById(elementId);
                copyText.select();
                copyText.setSelectionRange(0, 99999); // For mobile devices
                document.execCommand("copy");
                alert("Copied the text: " + copyText.value); // User feedback
            }

            // --- File Upload Logic (Virement Bancaire) ---
            $('#virement-upload-area').on('click', function() {
                $('#proof_of_payment').click();
            });

            $('#proof_of_payment').on('change', function(e) {
                handleFileUpload(e, 'virement-file-preview');
            });

            // --- File Upload Logic (D17) ---
            $('#d17-upload-area').on('click', function() {
                $('#proof_of_payment_d17').click();
            });

            $('#proof_of_payment_d17').on('change', function(e) {
                handleFileUpload(e, 'd17-file-preview');
            });

            function handleFileUpload(event, previewContainerId) {
                const files = event.target.files;
                const previewContainer = $(`#${previewContainerId}`);
                previewContainer.empty();

                if (files.length > 0) {
                    Array.from(files).forEach(file => {
                        const fileName = file.name;
                        const fileSize = (file.size / 1024).toFixed(2); // in KB

                        const fileElement = `
                            <div class="file-item flex items-center justify-between p-2 border rounded mt-2">
                                <span class="file-name text-sm text-gray-700">${fileName} (${fileSize} KB)</span>
                                <button type="button" class="remove-file-btn text-red-500 hover:text-red-700 text-lg ml-2" data-file-name="${fileName}">×</button>
                            </div>
                        `;
                        previewContainer.append(fileElement);
                    });
                }
            }

            $(document).on('click', '.remove-file-btn', function() {
                const fileNameToRemove = $(this).data('file-name');
                $(this).closest('.file-item').remove();
            });


            $(document).ready(function() {
                const initiallySelectedMethod = $('input[name="payment_method"]:checked').val();

                $('#proceedToPaymentBtn').hide();
                $('#proceedToPaymentShowDivBtn').hide();

                $('#initial-right-half-content').show();
                $('#payment-details-sections').hide();
                $('#payment-details-konnect-only').hide();
                $('#payment-details-virement').hide();
                $('#payment-details-d17').hide();
                $('#payment-details-espece').hide();

                if (initiallySelectedMethod === 'konnect') {
                    $('#proceedToPaymentBtn').show();
                } else if (initiallySelectedMethod) {
                    $('#proceedToPaymentShowDivBtn').show();
                }
            });
            $('.return-to-main-btn').click(function() {
                $('#payment-details-sections').hide();
                $('#initial-right-half-content').show();
                $('input[name="payment_method"][value="konnect"]').prop('checked', true);
            });

            // Copy to clipboard function
            window.copyToClipboard = function(elementId) {
                const element = document.getElementById(elementId);
                element.select();
                element.setSelectionRange(0, 99999);
                document.execCommand('copy');
                
                // Show feedback
                const button = element.nextElementSibling;
                const originalHTML = button.innerHTML;
                button.innerHTML = '<span style="color: green;">✓</span>';
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 1000);
            };

            $('#confirm-virement-btn, #confirm-d17-btn, #confirm-espece-btn').click(function() {
                const buttonId = $(this).attr('id');
                let paymentMethod = '';
                let uploader = null;

                if (buttonId === 'confirm-virement-btn') {
                    paymentMethod = 'virement_bancaire';
                    uploader = fileUploadvirementuploadarea;
                } else if (buttonId === 'confirm-d17-btn') {
                    paymentMethod = 'd17';
                    uploader = fileUploadd17uploadarea;
                } else if (buttonId === 'confirm-espece-btn') {
                    paymentMethod = 'espece';
                }

                // Pour virement et d17, un fichier est requis
                if (uploader && uploader.files.length === 0) {
                    alert('Veuillez télécharger une preuve de paiement.');
                    return;
                }

                submitManualPayment(paymentMethod, uploader ? uploader.files[0].file : null, $(this));
            });

             // Fonction de soumission pour les paiements manuels
            function submitManualPayment(method, file, button) {
                const originalButtonText = button.text();
                button.text('Envoi en cours...').prop('disabled', true);

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('payment_method', method);
                formData.append('email', $('#email').val());
                formData.append('phone', $('#phone').val());
                formData.append('course_id', $('#proceedToPaymentBtn').data('course-id'));
                if (file) {
                    formData.append('proof_file', file);
                }

                $.ajax({
                    url: "{{ route('payment.confirm_manual') }}", // Assurez-vous que cette route existe
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert(response.message || 'Votre demande a été soumise avec succès. Nous la vérifierons bientôt.');
                        window.location.href = '/'; // Rediriger vers la page d'accueil ou une page de remerciement
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Une erreur est survenue. Veuillez réessayer.';
                        alert(errorMsg);
                        button.text(originalButtonText).prop('disabled', false);
                    }
                });
            }
        });
    </script>
</body>
</html>

