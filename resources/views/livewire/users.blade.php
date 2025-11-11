@extends('layout.master')

@section('title', 'Gestion des Utilisateurs')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/filepond/filepond.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/filepond/image-preview.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/toastify/toastify.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/select/select2.min.css') }}">

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('main-content')
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
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
    }

    .profile-pic-container {
        position: relative;
        display: inline-block;
        padding: 5px;
    }

    .profile-pic-container:hover::after {
        content: 'Ajouter une photo';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border-radius: 5px;
        font-size: 12px;
        pointer-events: none;
        z-index: 2;
    }
   
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control, .form-select {
        border-left: none;
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: var(--bs-danger-text, #dc3545);
    }

    .is-invalid + .invalid-feedback,
    .is-invalid ~ .invalid-feedback {
        display: block !important; /* Show when input or select has is-invalid */
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }
    .modal-footer {
        border-top : 0;
    }
</style>
<div class="container-fluid">
    <div class="row m-1" style="margin-bottom:20px !important;">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="main-title">Gestion des Utilisateurs</h4>
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createAgentModal">
                Ajouter un utilisateur
            </button>
        </div>
    </div>

    <div>
        <!-- General Error Message -->
        @if($errors->has('general'))
            <div class="alert alert-danger" style="margin-bottom: 15px;">
                {{ $errors->first('general') }}
            </div>
        @endif

        @if($errors->has('error'))
            <div class="alert alert-danger" style="margin-bottom: 15px;">
                {{ $errors->first('error') }}
            </div>
        @endif

        <!-- Success Message -->
        @if(session()->has('message'))
            <div class="alert alert-success" style="margin-bottom: 15px;">
                {{ session('message') }}
            </div>
        @endif

        <div class="col-lg-12 col-lg-8">
            <div class="product-wrapper-grid">
                <div class="row">
                    @foreach($users as $user)
                        <div class="col-xxl-3 col-md-4 col-sm-6" style="margin-bottom:20px !important;">
                            <div class="card overflow-hidden shadow" style="height: 100%;background:#F7F9FB !important;border-radius:10px;">
                                <div class="card-body p-0">
                                    <div class="transparent-box" style="position: relative; left: 85%; top: 0%;">
                                        <div class="caption">
                                            @if($user->role == 'admin') 
                                                <img src="{{ asset('img/icon_admin.png') }}">
                                            @else
                                                <div class="col-lg-12" style="height: 20px;"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="product-content-box">
                                        <div class="product-grid mb-4">
                                            <div class="product-image left-main-img img-box" style="display: flex; flex-direction: row; flex-wrap: nowrap; align-content: flex-start; justify-content: center; align-items: flex-start;background:#F7F9FB !important;">
                                                @if($user->path_photo)
                                                    <img src="https://maxskills.tn/{{ $user->path_photo }}" alt="" style="max-height:125px;border-radius:50%;width: 45% !important;margin-top: 25px;">
                                                @else
                                                    <img src="{{ asset('assets/images/ai_avtar/2.jpg') }}" alt="" style="max-height:125px;border-radius:50%;width: 45% !important;margin-top: 25px;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-3" style="padding-top: 0px !important; padding-bottom: 10px !important;">
                                            <div class="profile-friends">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 ps-2" style="display: flex; flex-direction: column; flex-wrap: nowrap; justify-content: center; align-items: center;">
                                                        <div class="row fw-medium">{{ $user->firstname.' '.$user->name }}</div>
                                                        <div class="row text-muted f-s-12">{{ $user->niveau ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <a href="{{ route('update_profil_user', $user->id) }}" class="btn btn-primary" style="border-radius:0px 0px 10px 10px !important;padding:5px;font-size: 16px;color:white;width:-webkit-fill-available;font-weight:400;"> Modifier Profile </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for creating a new agent -->
    <div class="modal fade" id="createAgentModal" tabindex="-1" aria-labelledby="createAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAgentModalLabel">Ajouter un Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="save-agent" enctype="multipart/form-data">
                        @csrf
                        <!-- Profile Picture with Upload -->
                        <div class="mb-4 text-center">
                            <div class="profile-pic-container">
                                <img src="{{ asset('assets/images/ai_avtar/2.jpg') }}" 
                                     alt="Profile Picture" 
                                     class="profile-pic rounded-circle" 
                                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;" 
                                     id="profile-pic-preview">
                                <input type="file" name="path_photo" id="path_photo" class="filepond" style="display: none;">
                            </div>
                            <span class="invalid-feedback" id="path_photo-error"></span>
                            <p class="col-lg-12">Formats JPEG, PNG, jusqu'à 2 Mo</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Prénom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" name="firstname" class="form-control" id="firstname" value="{{ old('firstname') }}" required>
                                </div>
                                <span class="invalid-feedback" id="firstname-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom de famille</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                                </div>
                                <span class="invalid-feedback" id="name-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
                                </div>
                                <span class="invalid-feedback" id="email-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                                </div>
                                <span class="invalid-feedback" id="phone-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Localisation</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
                                    <input type="text" name="location" class="form-control" id="location" value="{{ old('location') }}">
                                </div>
                                <span class="invalid-feedback" id="location-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="niveau" class="form-label">Poste</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-briefcase"></i></span>
                                    <input type="text" name="niveau" class="form-control" id="niveau" value="{{ old('niveau') }}" placeholder="Designer" required>
                                </div>
                                <span class="invalid-feedback" id="niveau-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-shield"></i></span>
                                    <select name="role" class="form-select select2" id="role" required>
                                        <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    </select>
                                </div>
                                <span class="invalid-feedback" id="role-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-status-change"></i></span>
                                    <select name="status" class="form-select select2" id="status" required>
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Actif</option>
                                        <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div>
                                <span class="invalid-feedback" id="status-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                </div>
                                <span class="invalid-feedback" id="password-error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmez le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                                </div>
                                <span class="invalid-feedback" id="password_confirmation-error"></span>
                                <span class="invalid-feedback" id="password-match-error"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="submit-agent">Ajouter l'Agent</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

<!-- filepond -->
<script src="{{ asset('assets/vendor/filepond/file-encode.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/validate-size.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/validate-type.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/exif-orientation.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/image-preview.min.js') }}"></script>
<script src="{{ asset('assets/vendor/filepond/filepond.min.js') }}"></script>

<!-- toastify -->
<script src="{{ asset('assets/vendor/notifications/toastify-js.js') }}"></script>
<script src="{{ asset('assets/vendor/toastify/toastify.js') }}"></script>

<!-- phosphor js -->
<script src="{{ asset('assets/vendor/phosphor/phosphor.js') }}"></script>
<script src="{{ asset('assets/vendor/sortable/Sortable.min.js') }}"></script>

<!-- select2 -->
<script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>

<!-- Bootstrap JS (required for modal functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Form Handling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FilePond Initialization
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

        // Real-time Password Confirmation Validation
        const passwordInput = document.querySelector('#password');
        const confirmPasswordInput = document.querySelector('#password_confirmation');
        const passwordMatchError = document.querySelector('#password-match-error');
        const submitButton = document.querySelector('#submit-agent');

        function validatePasswords() {
            if (!passwordInput || !confirmPasswordInput || !passwordMatchError || !submitButton) {
                return; // Exit if elements are not found
            }

            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password && confirmPassword) {
                if (password !== confirmPassword) {
                    passwordMatchError.textContent = 'Les mots de passe ne correspondent pas.';
                    confirmPasswordInput.classList.add('is-invalid');
                    submitButton.disabled = true;
                } else {
                    passwordMatchError.textContent = '';
                    confirmPasswordInput.classList.remove('is-invalid');
                    submitButton.disabled = false;
                }
            } else {
                passwordMatchError.textContent = '';
                confirmPasswordInput.classList.remove('is-invalid');
                submitButton.disabled = false;
            }
        }

        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);
        }

        // Form submission via AJAX
        $('#save-agent').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitButton = form.querySelector('#submit-agent');
            submitButton.disabled = true; // Disable button to prevent multiple submissions

            // Clear previous errors
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
            const errorElements = form.querySelectorAll('.invalid-feedback');
            errorElements.forEach(element => {
                element.textContent = '';
            });

            let formData = new FormData(this);

            // Get file from FilePond and append it manually
            const file = pond.getFiles()[0]?.file;
            if (file) {
                formData.append('path_photo', file);
            }

            $.ajax({
                url: "{{ route('create_agent') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Toastify({
                            text: response.message || "Agent créé avec succès !",
                            duration: 3000,
                            backgroundColor: "green",
                        }).showToast();

                        setTimeout(() => {
                            $('#createAgentModal').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        Toastify({
                            text: response.message || "Erreur lors de la création de l'agent.",
                            duration: 5000,
                            backgroundColor: "red",
                        }).showToast();
                    }
                    submitButton.disabled = false; // Re-enable button
                },
                error: function (xhr) {
                    console.log('Error response:', xhr.responseJSON); // Debug response
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            console.log(field);
                            console.log('*******');
                            const input = form.querySelector(`[name="${field}"]`);
                            const errorElement = document.getElementById(`${field}-error`);
                            console.log(`Field: ${field}, Input:`, input, 'Error Element:', errorElement); // Debug
                            if (input && errorElement) {
                                input.classList.add('is-invalid');
                                errorElement.textContent = errors[field][0];
                                errorElement.style.display = 'block';
                            } else {
                                console.warn(`Input or error element not found for field: ${field}`);
                            }
                        }
                    } else {
                        Toastify({
                            text: xhr.responseJSON?.message || 'Une erreur est survenue lors de la création.',
                            duration: 5000,
                            backgroundColor: "red",
                        }).showToast();
                    }
                    submitButton.disabled = false; // Re-enable button
                }
            });
        });

        const form = document.querySelector('#save-agent');
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            input.addEventListener('click', function() {
                const errorElements = form.querySelectorAll('.invalid-feedback');
                errorElements.forEach(element => {
                    element.style.display = 'none';
                });
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(invalidInput => {
                    invalidInput.classList.remove('is-invalid');
                });
            });
        });
    });
</script>
@endsection