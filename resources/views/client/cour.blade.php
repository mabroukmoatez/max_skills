@extends('client.layout.master')
@section('title', $cour->title)
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
         #chat-input:focus {
            outline: none !important;
            box-shadow: none !important; 
            border-color: transparent !important; 
        }
        #profileModal .modal-content {
            background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgb(0 18 29) 100%) !important;
        }
        .chapter-card.locked,.chapter-card.lockednew {
            cursor: not-allowed;
        }
        .chapter-card.locked .card-img-top {
            position: relative;
            filter: grayscale(100%);
            opacity: 0.5;
            cursor: not-allowed;
        }

        .chapter-card.locked .card-img-container::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 80%;
            background-color: rgba(50, 50, 50, 0.5);
            z-index: 1;
        }

        .chapter-card.locked .lock-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2; /* Se place au-dessus de la superposition */

            /* Styles spécifiques pour une icône Font Awesome */
            font-size: 50px;  /* Utilisez font-size pour contrôler la taille */
            color: #ffffff;   /* Définissez la couleur de l'icône */
            opacity: 0.9;
        }
        .card-img-container {
            position: relative; /* <-- AJOUTEZ CECI */
            height: 100%; /* Assure que le conteneur prend toute la hauteur disponible */
        }
    </style>
@endsection
@section('main-content')
    <div class="promo-bar">
        <strong>{{ $cour->top_bar }}</strong>
    </div>

    @include('client.include.nav-bar')

    <div class="hero-section" style="background-image: url('{{ asset($cour->path_banner) }}');">
        <div class="hero-content">
            @php
                $title = $cour->title;
                $keyword = $cour->keyword;
                $keywordPosition = strpos($title, $keyword);

                if ($keywordPosition !== false) {
                    $beforeKeyword = substr($title, 0, $keywordPosition);

                    $afterKeyword = substr($title, $keywordPosition + strlen($keyword));

                    $keywordHtml =
                        '<div class="orange-box" style="display: inline-block; vertical-align: middle;">' .
                        $keyword .
                        '<div class="corner-circle top-left"></div>' .
                        '<div class="corner-circle top-right"></div>' .
                        '<div class="corner-circle bottom-left"></div>' .
                        '<div class="corner-circle bottom-right"></div>' .
                        '<div class="arrow"></div>' .
                        '</div>';
                } else {
                    $beforeKeyword = $title;
                    $keywordHtml = '';
                    $afterKeyword = '';
                }
            @endphp

            <h1 class="display-4 mb-4" style="font-weight: revert;">
                {{ $beforeKeyword }}
                {!! $keywordHtml !!}
                {{ $afterKeyword }}
            </h1>
            <p class="lead mb-4" style="font-size: 88%;color:#f5f5f59e;font-weight:lighter;">{{ $cour->description }}</p>
            <div class="text-center">
                <button class="btn custom-btn" id="custom-btn" style="width:40%;">{{ $cour->button }}</button>

            </div>
        </div>
    </div>

    <div class="container card-section">
        <div class="row" style="margin-bottom:2rem;">
            @foreach ($chapitres as $chapitre)
                <div class="col-lg-2-4 col-md-6 col-12 mb-4">
                    @if ($chapitre->status == 1 && ((auth()->user()->is_demo == 0 || auth()->user()->is_demo == 2) || $chapitre->id == 67 || $chapitre->id == 68))
                        <a href="{{ route('chapitreById', ['id' => $chapitre->id]) }}"
                            class="text-decoration-none chapter-link">
                            <div class="card chapter-card h-100">
                                <div class="card-img-container">
                                    <img src="{{ $chapitre->path_banner }}" class="card-img-top card-img-bottom img-fluid"
                                        alt="{{ $chapitre->title }}">
                                </div>
                                <div class="card-body-chapter">
                                    <div class="card-title">{{ $chapitre->title }}</div>
                                </div>
                            </div>
                        </a>
                    @else
                        {{-- Ceci est le bloc pour un chapitre VERROUILLÉ --}}
                        <div class="card chapter-card h-100 lockednew" onclick="showLockedAlert()">
                            <div class="card-img-container">
                                {{-- Image de fond du chapitre --}}
                                <img src="{{ asset($chapitre->path_banner) }}"
                                    class="card-img-top card-img-bottom img-fluid" alt="{{ $chapitre->title }}">

                                {{-- VOTRE ICÔNE EST INTÉGRÉE ICI --}}
                                {{-- <img class="fa-solid fa-lock" src="{{ asset('img/lock.png')}}" style="position: absolute;z-index: 9999;max-width: 20%;max-height: 14%;border: none;opacity:80%;"> --}}

                            </div>
                            <div class="card-body-chapter">
                                <div class="card-title">{{ $chapitre->title }}</div>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach

            @foreach ($certifica as $certifica_details)
                <div class="col-lg-2-4 col-md-6 col-12 mb-4">
                    <div class="card chapter-card h-100">
                        <div class="card-img-container">
                            <img src="{{ $certifica_details->path_banner }}" class="card-img-top card-img-bottom img-fluid"
                                alt="{{ $certifica_details->title }}">
                        </div>
                        <div class="card-body-chapter">
                            <div class="card-title">{{ $certifica_details->title }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @auth
        @if (auth()->user()->role !== 'admin')
            <!-- Updated chat window HTML structure -->
            <div class="chat-bubble" onclick="toggleChat()"> <img id="chat-toggle-icon"
                    src="{{ asset('client/svg/icon-robot.png') }}" alt="Photo" class="photo-icon"></div>
            <div class="chat-window" id="chat-window" style="display: none;">
                <div class="chat-top-bar">
                    <img src="{{ $photo_admin }}" alt="Avatar" class="avatar">
                    <div>
                        <div class="title">Mohamed | Consultant Support
                            <a href="https://wa.me/21624119500">
                                <i class="fa-solid fa-info-circle info-icon" style="position: relative;left: 23%;"></i>
                            </a>
                        </div>
                        <div class="status">En ligne</div>
                    </div>
                </div>
                <div class="chat-messages" id="chat-messages"></div>
                <div id="upload-progress-container" style="display: none; padding: 5px 15px;">
                    <div style="font-size: 12px; color: #ccc; margin-bottom: 3px;">Téléversement en cours...</div>
                    <div class="progress-bar-background" style="background-color: #555; border-radius: 5px; height: 6px;">
                        <div id="upload-progress-bar" class="progress-bar-foreground" style="background-color: #f7852c; width: 0%; height: 100%; border-radius: 5px; transition: width 0.2s;"></div>
                    </div>
                </div>
                <div class="chat-input">
                    <div class="chat-input-wrapper">
                        <img src="{{ asset('client/svg/photo_svg.png') }}" alt="Photo" class="photo-icon" id="photo-icon"
                            style="cursor: pointer;">
                        <input type="file" id="file-input" accept="image/*" style="display: none;">
                        <textarea id="chat-input" placeholder="Message..." rows="1" style="overflow-y: hidden; width: 240px; background: transparent; border: 0px; color: white;"></textarea>
                        <div class="chat-input-icons">
                            <i class="fa-solid fa-paperclip"></i>
                        </div>
                    </div>
                    <button onclick="sendMessage()">
                        <img src="{{ asset('client/svg/send_btn_svg.png') }}" alt="Send">
                    </button>
                </div>
            </div>
        @endif
    @endauth
    @include('client.include.footer')

@endsection
@section('script')
    <script>
        window.userData = {
            id: {{ auth()->id() }},
            name: '{{ auth()->user()->name }}',
            firstname: '{{ auth()->user()->firstname ?? '' }}',
            path_photo: '{{ auth()->user()->path_photo ?? '' }}'
        };
    </script>
    <script src="https://cdn.ably.com/lib/ably.min-2.js"></script>
    @include('client.include.script')

    <script>
        function showLockedAlert() {
            alert("Ce chapitre est actuellement verrouillé.");
        }
        document.addEventListener("DOMContentLoaded", function() {
            const customButton = document.getElementById('custom-btn');
            const firstChapterLink = document.querySelector('.chapter-link');
            customButton.addEventListener('click', function(event) {
                event.preventDefault();
                if (firstChapterLink) {
                    firstChapterLink.click();
                } else {
                    console.error('No chapters found.');
                }
            });
            $('#payNowBtn').on('click', function() {
                var price = $(this).data('price');
                var courseId = $(this).data('course-id');
                var firstName = $(this).data('first-name');
                var lastName = $(this).data('last-name');
                var email = $(this).data('email');
                var phoneNumber = $(this).data('phone-number');


                if (!price) {
                    alert('Payment amount not found.');
                    return;
                }

                $(this).text('Processing...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('konnect.initiate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: price,
                        course_id: courseId,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        phone_number: phoneNumber
                    },
                    success: function(response) {
                        if (response.success && response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            alert('Payment initiation failed: ' + (response.message ||
                                'Unknown error.'));
                            $('#payNowBtn').text('Pay Now').prop('disabled', false);
                        } 
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", status, error, xhr.responseText);
                        alert('An error occurred during payment. Please try again.');
                        $('#payNowBtn').text('Pay Now').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
