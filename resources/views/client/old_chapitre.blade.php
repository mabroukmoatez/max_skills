@extends('client.layout.master')
@section('title', $cour->title)
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.32.6/filepond.css" rel="stylesheet" />
<style>
    /* Your existing CSS styles remain unchanged */
    .modal-lg {
        max-width: 50% !important;
    }
    .modal-content {
        background-color: #010211 !important;
        box-shadow: 0px 4px 38px 0px #F8994F0A;
        border-radius: 25px !important;
    }
    .lesson-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .lesson-modal-cover {
        width: 100%;
        max-height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }
    .lesson-modal-title {
        font-size: 20px;
        font-weight: bold;
    }
    .lesson-modal-btns {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .lesson-modal-btns .btn {
        border-radius: 50px !important;
        height: 38px !important;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 24px;
        border-radius: 5px;
        white-space: nowrap;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .lesson-modal-btns .btn.custom-btn-white {
        background-color: white !important;
        color: black !important;
        border: 1px solid #ccc;
    }
    .lesson-modal-btns .btn.custom-btn-white:hover {
        background-color: #f0f0f0 !important;
        color: black !important;
    }
    @media (max-width: 768px) {
        .lesson-modal-btns .btn {
            height: 45px;
            font-size: 14px;
            padding: 6px 12px;
        }
    }
    @media (min-width: 768px) {
        /* These styles apply ONLY if the screen width is 768px or wider */
        .hero-content-left {
            max-width: 40%; /* On tablets, it takes up 40% of the width */
            text-align: right; /* Align text to the right */
        }
    }

    /* --- Desktop Styles --- */
    @media (min-width: 1200px) {
        /* These styles apply ONLY if the screen width is 1200px or wider */
        .hero-content-left {
            max-width: 30%; /* On large desktops, it's 30% */
        }
    }
    .lesson-modal-description {
        margin-top: 20px;
        color: #F5F5F5;
    }
    .custom-btn-project {
        background: radial-gradient(141.42% 141.42% at 0% 0%, rgba(248, 153, 79, 0.57) 0%, rgba(255, 255, 255, 0) 100%) !important;
        box-shadow: 0px 0px 24px -10px rgba(248, 153, 79, 0.57) !important;
        border-radius: 50px !important;
        font-weight: bold !important;
        color: #fff !important;
        cursor: pointer !important;
        width: 100%;
        font-weight: 600 !important;
    }
    .custom-btn-project:hover {
        background: radial-gradient(141.42% 141.42% at 0% 0%, rgba(248, 153, 79, 0.75) 0%, rgba(255, 255, 255, 0) 100%) !important;
        box-shadow: 0px 0px 30px -10px rgba(248, 153, 79, 0.75) !important;
    }
    .hero-section {
        display: flex;
        flex-direction: row;
        align-content: flex-end;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 3%;
    }
    .hero-with-gradient {
        min-height: 60vh;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-color: #000;
        position: relative; 
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .hero-with-gradient::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 60%, #010211 100%);
        pointer-events: none;
    }
    .hero-content-left {
        flex: 1;
        max-width: 30%;
        text-align: right;
    }
    .hero-content-right {
        flex: 2;
        text-align: left;
    }
    .hero-content-left button {
        width: 250px;
        margin: 5px 0;
        height: 40px;
    }
    .lesson-header {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }
    .lesson-header span.active {
        font-weight: bold;
        text-decoration: underline;
        color: #FF9900;
    }
    .lesson-header span.gray {
        color: #888;
        cursor: pointer;
    }
    .card.chapter-card {
        display: flex;
        align-items: center;
        margin: 10px auto;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card.chapter-card img {
        width: 80%;
        height: 200px;
        object-fit: fill;
        border-radius: 5%;
        max-height:295px;
    }
    .card-body {
        flex: 1;
        text-align: left;
    }
    .card-body p {
        margin: 5px 0;
    }
    @media (max-width: 768px) {
        .hero-section {
            flex-direction: column;
        }
        .hero-content-left,
        .hero-content-right {
            max-width: 100%;
            text-align: center;
        }
        .card.chapter-card {
            flex-direction: column;
            align-items: center;
        }
        .card-img-container img {
            margin-right: 0;
            margin-bottom: 10px;
        }
        .modal-lg {
            max-width: 95% !important;
        }
        .modal-body {
            padding: 5px 23px 12px 23px;
        }
    }
    .lesson-modal-header-image {
        width: 100%;
        max-height: 357px;
        object-fit: fill;
        border-top-left-radius: 25px;
        border-top-right-radius: 25px;
    }
    .modal-header {
        padding: 0 !important;
        border-bottom: none !important;
    }
    .modal-body {
        box-shadow: 0px -8px 15px -5px rgba(0, 0, 0, 1);
        padding: 30px 53px 40px 53px;
    }
    .modal-backdrop {
        display: none;
        background-color: #000 !important;
        opacity: 0.4 !important;
    }
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #000;
        z-index: 999;
        opacity: 0.8;
    }
    .fancybox__content {
        width: 100% !important;
        height: 100% !important;
    }
    .fancybox-slide.custom-video-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw !important;
        height: 100vh !important;
    }
    .fancybox-slide.custom-video-slide video {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }
    .form-control {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
    }
    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
    }
    .form-control::placeholder {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
        opacity: 1 !important;
    }
    .form-control::-webkit-input-placeholder {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
        opacity: 1;
    }
    .form-control::-moz-placeholder {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
        opacity: 1;
    }
    .form-control:-ms-input-placeholder {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
        opacity: 1;
    }
    .form-control:-moz-placeholder {
        background-color: rgba(255, 255, 255, 0.08);
        color: #fff !important;
        opacity: 1;
    }
    .ProjectModal .modal-content {
        box-shadow: 0px 0px 36px 0px #F8994F73 !important;
        background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgb(0 18 29) 100%) !important;
    }
    .ProjectSucceeModal .modal-content {
        box-shadow: 0px 0px 36px 0px #F8994F73 !important;
        background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgb(0 18 29) 100%) !important;
    }
    .filepond--drop-label {
        background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgb(0 18 29) 100%) !important;
        color: #fff;
    }
    .filepond--list {
        display: none !important;
    }
    .filepond--list-scroller {
        display: none !important;
    }
    .filepond--droppable .filepond--panel-bottom.filepond--panel-root {
        display: none !important;
    }
    option.custom-option {
        background-color: #000 !important;
        color: #fff !important;
    }
    .custom-progress-bar {
        background-color: #F8994F !important;
    }
    .upload-container {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        color: white;
        max-width: 98%;
        border: 2px dashed #F8994F66;
    }
    .drop-zone {
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    .file-item {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ti.ti-file {
        margin-right: 10px;
        font-size: 20px;
        position: relative;
        top: -15px;
    }
    .file-content {
        flex-grow: 1;
    }
    .progress {
        height: 4px;
        background-color: #fff;
        flex-grow: 1;
    }
    .progress-bar {
        background-color: #ff6b00;
        height: 100%;
        transition: width 0.3s ease;
    }
    .delete-btn {
        color: #ff4444;
        cursor: pointer;
        font-size: 1.2rem;
    }
    .file-name {
        font-size: 0.8rem;
        text-align: left;
    }
    .file-size {
        font-size: 0.8rem;
        color: #cecaca;
        margin-bottom: 10px;
        text-align: left;
    }
    .success-icon {
        margin-left: 10px;
        color: #ff6b00;
        font-size: 20px;
    }
    .success-icon .ti.ti-circle-check {
        vertical-align: middle;
    }
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #000;
        opacity: 0.15;
        z-index: 0;
    }
    option:hover {
        background-color: #ff6b00 !important;
        color: #ffffff !important;
    }
    .ProjectModal .form-control {
        font-size: 0.9rem !important;
        border: var(--bs-border-width) solid rgba(255, 255, 255, 0.25) !important;
    }

</style>
@endsection

@section('main-content')
<!-- Promotion Bar -->
<div class="promo-bar">
    <strong>{{ $cour->top_bar }}</strong>
</div>

@include('client.include.nav-bar')

<div class="modal-backdrop"></div>
<!-- Hero Section -->
<div class="hero-section hero-with-gradient row" style="background-image:url('https://maxskills.tn{{ $chapitre->path_resume  }}');">

    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #000; opacity: 0.65; z-index: 0;"></div>
    <div class="hero-content-left col-lg-4" style="margin-left:3%;z-index: 99;">
        @if(count($lessons) > 0)
            <button class="btn custom-btn" style="font-weight:600;" onclick="openFirstModal()">Commencer</button>
        @else
            <button class="btn custom-btn" style="font-weight:600;" disabled>Commencer</button>
        @endif
        <button class="btn custom-btn-project" onclick="openProjectModal()">Envoyer un projet</button>
    </div>

    <div class="hero-content-right col-lg-8" style="margin-right: 7%;margin-left: 3%;z-index: 99;">
        <h1>{{ $chapitre->title }}</h1>
        <p class="lead mb-4" style="font-size: 88%; color:#f5f5f59e; font-weight:lighter;">
            {{ $chapitre->description }}
        </p>
        <p class="lead mb-4" style="font-size: 88%; color:#f5f5f59e; font-weight:lighter;">
            @if(!empty($totalDuration))
                <img src="{{ asset('client/images/polygon.png') }}" style="margin-right:10px;width: 2%;">{{ $totalDuration }}.
            @endif
        </p>
    </div>
</div>

<!-- Lesson Header -->
<div class="lesson-header text-center" style="
    background-color: #010211;
    box-shadow: inset 0 50px 50px -20px rgba(0, 0, 0, 0.5);
    position: relative;
    padding: 20px 0;
">
    <span class="active" style="color:#fff;">Leçons</span>
    <span class="gray" onclick="window.location.href='{{ route('courById', ['id' => $cour->id]) }}'">Plus de tutoriel</span>
</div>

<!-- Chapter Cards Section -->
<div class="container card-section" style="margin-bottom:5% !important;">
    <div class="row" id="gallery">
        @foreach($lessons as $lesson)
            <div class="col-lg-12">
                <div class="card chapter-card">
                    <div class="card-body d-flex" style="padding: 0 !important; height: 200px;">
                        <div class="col-lg-4 d-flex justify-content-center align-items-center clickable-div"
                            data-toggle="modal"
                            data-target="#lessonModal-{{ $lesson->id }}"
                            style="height: 100%; cursor: pointer;">
                            <img src="{{ $lesson->path_icon }}" alt="{{ $lesson->title }}" class="img-fluid">
                        </div>
                        <div class="col-lg-8 d-flex flex-column justify-content-start" style="height: 100%;">
                            <p class="fw-bold mb-1" style="font-size:22px !important;">{{ $lesson->title }}</p>
                            <div class="lesson-duration d-flex align-items-center mb-2">
                                @if((!empty($lesson->lessonVideoSeconds) && $lesson->lessonVideoSeconds != 0) || (!empty($lesson->lessonVideoMinutes) && $lesson->lessonVideoMinutes != 0) ||(!empty($lesson->lessonVideoHours) && $lesson->lessonVideoHours != 0))
                                    <img src="{{ asset('client/images/polygon.png') }}" class="me-2" style="width: 2%; height: auto;">
                                    @if(!empty($lesson->lessonVideoHours) && $lesson->lessonVideoHours != 0)
                                        <span class="me-sm-2">{{ $lesson->lessonVideoHours }}hr</span>
                                    @endif
                                    @if(!empty($lesson->lessonVideoMinutes) && $lesson->lessonVideoMinutes != 0)
                                        <span>{{ $lesson->lessonVideoMinutes }}min</span>
                                    @endif
                                    @if(!empty($lesson->lessonVideoSeconds) && $lesson->lessonVideoSeconds != 0)
                                        <span class="ms-2">{{ $lesson->lessonVideoSeconds }}sec.</span>
                                    @endif
                                @endif
                            </div>
                            <p class="text-white flex-grow-1">{{ $lesson->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="lessonModal-{{ $lesson->id }}" tabindex="-1" role="dialog" aria-labelledby="lessonModalLabel-{{ $lesson->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header p-0 border-0">
                            <img src="{{ $lesson->path_icon }}" alt="{{ $lesson->title }}" class="lesson-modal-header-image">
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="lesson-modal-title" style="margin-top: 10px !important;margin-bottom:17px !important;">{{ $lesson->title }}</h4>
                                    <div class="lesson-modal-btns d-flex gap-10 align-items-center">
                                      <a class="btn custom-btn-white" 
   style="margin-top: 0px !important;font-weight:600;" 
   href="{{ $lesson->path_video }}" 
   data-caption="{{ $lesson->title }}" 
   onclick="playVideo(event, this)">
   <i class="fa-solid fa-play" style="margin-right:10px;"></i>
   Lire le vidéo
</a>

                                        @if($lesson->path_projet)
                                        <a href="{{ $lesson->path_projet }}" class="btn custom-btn" style="margin-top: 0px !important;font-weight:600;" download>
                                            <i class="fa-solid fa-download" style="margin-right:10px;"></i>
                                            Télécharger Fichier Source
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="lesson-modal-description" style="font-size: 14px;color:#fff;">{{ $lesson->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="modal fade ProjectModal" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="ProjectModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h4 class="mb-3" style="font-weight: bold;margin-top:15px !important;">Envoyer fichier projet</h4>
                        <p class="mb-4" style="padding-left:100px !important;padding-right:100px !important;font-size:11px !important;">
                            Sélectionnez le chapitre et le projet concerné, téléversez vos fichiers, puis cliquez sur "Envoyer" pour transmettre votre travail au formateur, qui vous fournira un retour.
                        </p>
                        <form id="projectForm" action="#" method="POST" enctype="multipart/form-data" class="row g-3 justify-content-center">
                            @csrf
                            <div class="col-md-12 d-flex gap-2 justify-content-center">
                                <input type="text" class="form-control" placeholder="Chapitre" value="{{ $chapitre->title }}" style="line-height: 1.8;border-radius: .6rem;" readonly>
                                <select class="form-control" id="lesson_id" name="lesson_id" style="line-height: 1.8;border-radius: .6rem;">
                                    @foreach ($lessons as $lessonsProject)
                                        <option class="custom-option" value="{{ $lessonsProject->id }}">{{ $lessonsProject->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="upload-container">
                                <div class="drop-zone" id="dropzone">
                                    <img class="" src="{{ asset('img/upload-files.png') }}" style="background: #F5F5F514; border-radius: 50%; display: inline-block; line-height: 55px;padding:5px 10px 5px 10px;"></i>
                                    <div style="font-size: 15px !important;"><strong>Cliquez pour télécharger</strong> ou glisser-déposer</div>
                                    <div class="format-info" style="font-size: 12px !important;">Formats autorisés: JPEG, PNG, GIF, TXT, CSV, PDF, jusqu'à 2 Mo</div>
                                </div>
                            </div>
                            <div id="fileListContainer"></div>
                            <div class="col-12 py-2">
                                <button type="button" onClick="submitForm()" class="btn custom-btn mx-auto d-block" style="font-weight: 500 !important;padding:8px 40px 8px 40px !important;margin-top:0px !important;">
                                    <i class="ti ti-send"></i> Envoyer le projet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade ProjectSucceeModal" id="ProjectSucceeModal" tabindex="-1" role="dialog" aria-labelledby="ProjectSucceeModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button class="btn btn-sm b-3 my-3" style="
                            color: #F8994F;
                            background-color: #766d6691;
                            border-width: 0px, 2px, 2px, 0px;
                            border-style: solid;
                            border-color: rgba(248, 153, 79, 0.27);
                            box-shadow: 0px 0px 24px -10px rgba(248, 153, 79, 0.57);
                            border-radius: 200px;
                            font-size:17.37px;
                        ">
                            <i class="ti ti-info-circle"></i>Support
                        </button>
                        <h4 class="mb-3" style="font-weight: bold;margin-top:25px !important;">Le projet a été envoyé avec succès !</h4>
                        <p class="my-3 mb-4" style="padding-left:100px !important;padding-right:100px !important;font-size:20px !important;">
                            Notre équipe va le vérifier et vous recevrez une mise à jour bientôt.
                            Merci pour votre envoi ! 🚀
                        </p>
                        <div class="col-12 py-2">
                            <button type="button" onClick="ProjectModalShow()" class="btn custom-btn mx-auto d-block">
                                <i class="ti ti-send"></i> Envoyer un autre projet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @auth
        @if (auth()->user()->role !== 'admin')
            <div class="chat-bubble" onclick="toggleChat()">
                <img id="chat-toggle-icon" src="{{ asset('client/svg/row_bottom.png') }}" alt="Photo" class="photo-icon">
            </div>
            <div class="chat-window" id="chat-window" style="display: none;">
                <div class="chat-top-bar">
                    <img src="{{ $photo_admin }}" alt="Avatar" class="avatar">
                    <div>
                        <div class="title">Mohamed | Consultant Support <i class="fa-solid fa-info-circle info-icon" style="position: relative;left: 23%;"></i></div>
                        <div class="status">En ligne</div> 
                    </div>
                   
                </div>
                <div class="chat-messages" id="chat-messages"></div>
                <div class="chat-input">
                    <div class="chat-input-wrapper">
                        <img src="{{ asset('client/svg/photo_svg.png') }}" alt="Photo" class="photo-icon" id="photo-icon" style="cursor: pointer;">
                        <input type="file" id="file-input" accept="image/*" style="display: none;">
                        <input type="text" id="chat-input" placeholder="Message...">
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
</div>

@include('client.include.footer')
@endsection

@section('script')
<script src="https://cdn.ably.com/lib/ably.min-2.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.32.6/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script>
    window.userData = {
        id: {{ auth()->id() }},
        name: '{{ auth()->user()->name }}',
        firstname: '{{ auth()->user()->firstname ?? '' }}',
        path_photo: '{{ auth()->user()->path_photo ?? '' }}'
    };
</script>
  @include('client.include.script')
<script>
    // File upload and project submission logic
    const dropzone = document.getElementById('dropzone');
    const fileListContainer = document.getElementById('fileListContainer');
    let selectedFiles = [];
 
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    dropzone.addEventListener('drop', handleDrop);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    dropzone.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.multiple = true;
        input.onchange = (e) => handleFiles(e.target.files);
        input.click();
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (!['.js', '.php', '.html', '.blade.php'].includes(getFileExtension(file.name))) {
                addFileToList(file);
                uploadFileImmediately(file);
            } else {
                alert(`File type not allowed: ${file.name}`);
            }
        });
    }

    function getFileExtension(filename) {
        return '.' + filename.split('.').pop().toLowerCase();
    }

    function addFileToList(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        const fileIcon = document.createElement('i');
        fileIcon.className = 'ti ti-file';
        const contentContainer = document.createElement('div');
        contentContainer.className = 'file-content';
        const fileName = document.createElement('div');
        fileName.className = 'file-name';
        fileName.textContent = file.name;
        const fileSize = document.createElement('div');
        fileSize.className = 'file-size';
        fileSize.textContent = `(${formatBytes(file.size)})`;
        const progressBarContainer = document.createElement('div');
        progressBarContainer.className = 'progress';
        progressBarContainer.innerHTML = '<div class="progress-bar" role="progressbar" style="width: 0%"></div>';
        const deleteButton = document.createElement('span');
        deleteButton.className = 'delete-btn';
        deleteButton.textContent = '×';
        deleteButton.style.display = 'none';
        deleteButton.onclick = () => removeFile(deleteButton);
        const successIcon = document.createElement('span');
        successIcon.className = 'success-icon';
        successIcon.innerHTML = '<i class="ti ti-circle-check"></i>';
        successIcon.style.display = 'none';
        contentContainer.appendChild(fileName);
        contentContainer.appendChild(fileSize);
        contentContainer.appendChild(progressBarContainer);
        fileItem.appendChild(fileIcon);
        fileItem.appendChild(contentContainer);
        fileItem.appendChild(deleteButton);
        fileItem.appendChild(successIcon);
        fileListContainer.appendChild(fileItem);
        const fileData = {
            file: file,
            messageId: null,
            deleteButton: deleteButton,
            successIcon: successIcon,
            progressBar: progressBarContainer.querySelector('.progress-bar')
        };
        selectedFiles.push(fileData);
    }

    function removeFile(button) {
        const fileItem = button.closest('.file-item');
        const index = Array.from(fileListContainer.children).indexOf(fileItem);
        selectedFiles.splice(index, 1);
        fileItem.remove();
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    async function uploadFileImmediately(file) {
        const formData = new FormData();
        formData.append('file', file);
        const index = selectedFiles.findIndex(item => item.file === file);
        const progressBar = fileListContainer.children[index].querySelector('.progress-bar');
        const fileData = selectedFiles[index];
        const deleteButton = fileData.deleteButton;
        const successIcon = fileData.successIcon;
        deleteButton.style.display = 'inline-block';
        successIcon.style.display = 'none';
        try {
            const response = await uploadFile(formData, progressBar);
            selectedFiles[index].messageId = response.message_id;
            deleteButton.style.display = 'none';
            successIcon.style.display = 'inline-block';
            console.log(`File uploaded: ${file.name}, Message ID: ${response.message_id}`);
        } catch (error) {
            console.error(`Error uploading file ${file.name}:`, error);
            deleteButton.style.display = 'inline-block';
            successIcon.style.display = 'none';
        }
    }

  

    async function submitForm() {
        const lessonId = document.getElementById('lesson_id').value;
        if (selectedFiles.length === 0) {
            alert('No files to submit!');
            return;
        }
        try {
            const response = await updateLessonIds(lessonId);
            console.log(response);
            selectedFiles = [];
            fileListContainer.innerHTML = '';
            $('#ProjectModal').modal('hide');
            $('#ProjectSucceeModal').modal('show');

            if (channel && response.messages && response.messages.length > 0) {
                response.messages.forEach((fileMsg) => {
                    const messageData = {
                        id: fileMsg.id, // Use server-provided ID
                        chat_id: chatId,
                        message: fileMsg.message, // e.g., "storage/uploads/...png"
                        is_admin: fileMsg.is_admin || false,
                        sent_at: fileMsg.sent_at || new Date().toISOString(),
                        sender_name: window.userData.name,
                        readed: fileMsg.readed || false,
                        sender: {
                            id: window.userData.id,
                            name: window.userData.name,
                            firstname: window.userData.firstname,
                            path_photo: window.userData.path_photo
                        }
                    };
                    console.log('Publishing message to channel:', `chat:${chatId}`, messageData);
                    channel.publish('message', messageData, function(err) {
                        if (err) {
                            console.error('Error publishing message:', err);
                        } else {
                            console.log('Message published successfully');
                            displayMessages([messageData]);
                            lastMessageId = Math.max(lastMessageId, fileMsg.id);
                        }
                    });
                });
            }
        } catch (error) {
            console.error('Error updating lesson IDs:', error);
            alert('Failed to update lesson IDs.');
        }
    }

    function ProjectModalShow() {
        $('#ProjectSucceeModal').modal('hide');
        $('#ProjectModal').modal('show');
    }

    async function updateLessonIds(lessonId) {
        const messageIds = selectedFiles.map(item => item.messageId).filter(id => id !== null);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('https://maxskills.tn/updateLessonId', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                message_ids: messageIds,
                lesson_id: lessonId
            })
        });
        if (!response.ok) {
            throw new Error('Failed to update lesson IDs');
        }
        return await response.json();
    }

    // Modal and video handling
    document.addEventListener('DOMContentLoaded', () => {
        function openLessonModal(lessonId) {
            $('#lessonModal-' + lessonId).modal('show');
        }
        $(document).ready(function() {
            $('.clickable-div').on('click', function() {
                var target = $(this).data('target');
                $(target).modal('show');
                $('.modal-backdrop').attr('style', 'display: block !important; opacity: 0.4 !important;');
            });
        });
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal-backdrop').css('display', 'none');
        });
    });

    function openFirstModal() {
        const firstLessonId = "{{ $lessons[0]->id ?? '' }}";
        $('#lessonModal-' + firstLessonId).modal('show');
        $('.modal-backdrop').attr('style', 'display: block !important; opacity: 0.4 !important;');
    }

    function openProjectModal() {
        $('#ProjectModal').modal('show');
        $('.modal-backdrop').attr('style', 'display: block !important; opacity: 0.4 !important;');
    }

function playVideo(e, el) {
    $('.modal.fade.show').modal('hide');
    $('.modal-backdrop').remove();

    e.preventDefault();

    // Create a dummy fullscreen wrapper
    const fullscreenWrapper = document.createElement('div');
    fullscreenWrapper.style.position = 'fixed';
    fullscreenWrapper.style.top = '0';
    fullscreenWrapper.style.left = '0';
    fullscreenWrapper.style.width = '100%';
    fullscreenWrapper.style.height = '100%';
    fullscreenWrapper.style.zIndex = '9999';
    fullscreenWrapper.style.backgroundColor = 'black'; // Optional: black background
    document.body.appendChild(fullscreenWrapper);

    // Create a <video> element
    const video = document.createElement('video');
    video.src = el.href;
    video.controls = true;
    video.autoplay = true;
    video.style.width = '100%';
    video.style.height = '100%';
    video.style.objectFit = 'contain'; // ensures the video fits nicely

    // Append video to the wrapper
    fullscreenWrapper.appendChild(video);

    // Request fullscreen on the wrapper
    fullscreenWrapper.requestFullscreen().catch(err => {
        console.warn("Fullscreen failed:", err);
        // If fullscreen fails, fallback — just show video normally
    });

    // Exit fullscreen and cleanup when video ends or user presses ESC
    const cleanup = () => {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }
        fullscreenWrapper.remove();
    };

    video.addEventListener('ended', cleanup);
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            cleanup();
        }
    });
}



</script>
@endsection