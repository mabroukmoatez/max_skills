@extends('layout.master')

@section('title', 'Modifier Utilisateur')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/filepond/filepond.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/filepond/image-preview.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/toastify/toastify.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/select/select2.min.css') }}">
<style>
    .modal {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover {
        color: black;
        cursor: pointer;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 50% !important;
        }
    }

    .profile-pic {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
    }

    .profile-pic-container {
        position: relative;
        display: inline-block;
    }

    .profile-pic-container:hover::after {
        content: 'Changer la photo';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
        pointer-events: none;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control {
        border-left: none;
    }

    .btn-update {
        background-color: #007bff;
        color: white;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: white;
    }
    .form-control:focus { 
        box-shadow: 0 0 0 0.1px rgba(160, 160, 176, 0.55);
    }
    .form-select:focus { 
        box-shadow: 0 0 0 0.1px rgba(160, 160, 176, 0.55);
    }
    .btn-profile {
        background-color: #F8994F;
        color: white;
        border: none;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
    }
     .btn-delete:hover {
        background-color:#dc3545;
        color: white;
        border: none;
    }
    .profile-row {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap; /* Allow wrapping on small screens */
    }

    .profile-details {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .profile-details h5 {
        margin: 0;
        font-size: 20px  !important;
    }

    .profile-details p {
        margin: 0;
        font-size: 16px !important;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="row m-1" style="margin-bottom:20px !important;">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="main-title">Informations sur le compte</h4>
                <p>Mettez à jour les informations de votre compte</p>
            </div>
           <button type="button" class="btn btn-delete mb-3" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="ti ti-trash"></i> Supprimer le profil
            </button>
        </div>
    </div>

    <!-- Blank start -->
    <div class="row">
        <!-- Default Card start -->
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Profile Picture with Upload -->
                <form id="update-user-form"  method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                    <div class="profile-row mb-4">
                        <div class="profile-pic-container">
                            <img src="{{ $user->path_photo ? 'https://maxskills.tn/' . $user->path_photo : asset('assets/images/ai_avtar/2.jpg') }}" 
                                alt="Profile Picture" 
                                class="profile-pic" 
                                id="profile-pic-preview">
                            <input type="file" name="path_photo" id="path_photo" class="filepond" style="display: none;">
                        </div>
                        <div class="profile-details">
                            <h5>{{ $user->firstname . ' ' . $user->name }}</h5>
                            <p class="text-muted">{{ $user->niveau ?? 'Designer' }}</p>
                        </div>
                        <div>
                            @error('path_photo') <span class="text-danger" id="path_photo-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <!-- General Error Message -->
                    @if($errors->has('general'))
                        <div class="alert alert-danger" style="margin-bottom: 15px;">
                            {{ $errors->first('general') }}
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if(session()->has('message'))
                        <div class="alert alert-success" style="margin-bottom: 15px;">
                            {{ session('message') }}
                        </div>
                    @endif

                    <h5 class="mb-3">Informations personnelles</h5>
                        @csrf
                        @method('POST')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Prénom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" name="firstname" class="form-control" id="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                                </div>
                                @error('firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom de famille</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Localisation</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
                                    <input type="text" name="location" class="form-control" id="location" value="{{ old('location', $user->location) }}">
                                </div>
                                @error('location') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="niveau" class="form-label">Poste</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-briefcase"></i></span>
                                    <input type="text" name="niveau" class="form-control" id="niveau" value="{{ old('niveau', $user->niveau) }}" placeholder="Designer" required>
                                </div>
                                @error('niveau') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-shield"></i></span>
                                    <select name="role" class="form-select" id="role" required>
                                        <option value="agent" {{ old('role', $user->role) == 'agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    </select>
                                </div>
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-status-change"></i></span>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Actif</option>
                                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password">
                                </div>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmez le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                                </div>
                                @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-update me-2">Mise à jour</button>
                            <a href="{{ url()->previous() }}" class="btn btn-cancel">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Default Card end -->
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer le profil de <strong>{{ $user->firstname . ' ' . $user->name }}</strong> ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-delete" id="confirmDeleteBtn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Blank end -->
</div>
@endsection

@section('script')
<!-- filepond -->
<script src="{{ asset('assets/vendor/filepond/file-encode.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/validate-size.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/validate-type.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/exif-orientation.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/image-preview.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/filepond.min.js') }}"></script>

<script src="{{ asset('assets/vendor/notifications/toastify-js.js') }}"></script>
<script src="{{ asset('assets/vendor/toastify/toastify.js') }}"></script>

<!-- phosphor js -->
<script src="{{ asset('assets/vendor/phosphor/phosphor.js') }}"></script>
<script src="{{ asset('assets/vendor/sortable/Sortable.min.js') }}"></script>

<!-- select2 -->
<script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>

<!-- js -->
<script src="{{ asset('assets/js/select.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        FilePond.registerPlugin(
            FilePondPluginFileEncode,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType,
            FilePondPluginImagePreview,
            FilePondPluginImageExifOrientation
        );

        const pond = FilePond.create(document.querySelector('#path_photo'), {
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/jpg'],
            maxFileSize: '1MB',
            allowFileEncode: false,
            allowProcess: false,
            server: null,
            instantUpload: false,
            imagePreviewHeight: 80,
            imageCropAspectRatio: '1:1',
            imageResizeTargetWidth: 80,
            imageResizeTargetHeight: 80,
            stylePanelLayout: 'compact circle',
        });

        // Hide the default FilePond UI
        pond.element.style.display = 'none';

        // Trigger file input on image click
        const profilePicPreview = document.querySelector('#profile-pic-preview');
        profilePicPreview.addEventListener('click', () => {
            pond.element.querySelector('input[type="file"]').click();
        });

        // Update the preview image when a new file is selected
        pond.on('addfile', (error, file) => {
            if (error) {
                console.log('FilePond error:', error);
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePicPreview.src = e.target.result;
            };
            reader.readAsDataURL(file.file);
        });

        $('#update-user-form').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            const file = pond.getFiles()[0]?.file;
            if (file) {
                formData.append('path_photo', file); // Append the selected file
            }

            let url = "{{ route('update_store_profil_user', $user->id) }}"; // replace with your actual route

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Toastify({
                        text: "Mise à jour réussie !",
                        duration: 3000,
                        backgroundColor: "green",
                    }).showToast();
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = '';
                    for (const key in errors) {
                        errorMsg += errors[key][0] + '\n';
                    }
                    Toastify({
                        text: errorMsg,
                        duration: 5000,
                        backgroundColor: "red",
                    }).showToast();
                }
            });
        });

       $('#confirmDeleteBtn').on('click', function () {
           
            let url = "{{ route('delete_profil_user', $user->id) }}"; // Adjust the route name as needed

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    Toastify({
                        text: "Profil supprimé avec succès !",
                        duration: 3000,
                        backgroundColor: "green",
                    }).showToast();
                    // Redirect to the users page after deletion
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.users') }}";
                    }, 1000);
                },
                error: function (xhr) {
                    let errorMsg = xhr.responseJSON.message || 'Une erreur est survenue lors de la suppression.';
                    Toastify({
                        text: errorMsg,
                        duration: 5000,
                        backgroundColor: "red",
                    }).showToast();
                }
            });

            // Close the modal
            $('#deleteConfirmModal').modal('hide');
        });

    });
</script>
@endsection