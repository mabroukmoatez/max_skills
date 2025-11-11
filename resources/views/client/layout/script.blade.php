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
            maxFileSize: '2MB',
            allowFileEncode: false,
            allowProcess: false,
            server: null,
            instantUpload: false,
            imagePreviewHeight: 80,
            imageCropAspectRatio: '1:1',
            imageResizeTargetWidth: 100,
            imageResizeTargetHeight: 100,
            stylePanelLayout: 'compact circle',
        });

        // Hide the default FilePond UI
        pond.element.style.display = 'none';

        // Trigger file input on image click
        const profilePicPreview = document.querySelector('#profile-pic-preview');
        profilePicPreview.addEventListener('click', () => {
            pond.browse(); // open the file picker
        });

        // Update image preview instantly
        pond.on('addfile', (error, file) => {
            if (error) {
                console.error('FilePond error:', error);
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePicPreview.src = e.target.result;
            };
            reader.readAsDataURL(file.file);
        });

        // Form submission with AJAX
        $('#update-user-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            const file = pond.getFiles()[0]?.file;
            if (file) {
                formData.append('path_photo', file);
            }

            let url = "{{ route('update_store_profil_client') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Remove existing modal if any
                    $('#successModal').remove();
                    $('#profileModal').hide();
                    // Append custom modal with spinner
                    $('body').append(`
        <div id="successModal" style="
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgba(0, 18, 29, 0.36) 100%);
            border: 2px solid #F8994F7D;
            border-radius: 30px;
            padding: 30px;
            color: white;
            z-index: 9999;
            text-align: center;
            font-size: 18px;
            min-width: 300px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        ">
            <div class="loader" style="
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            "></div>
            <p style="margin: 0;">Mise à jour réussie !</p>
        </div>

        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `);

                    // Hide and reload after 2 seconds
                    setTimeout(function() {
                        $('#successModal').fadeOut(300, function() {
                            $(this).remove();
                            location.reload(); // Reload the page
                        });
                    }, 2000);
                },

                error: function(xhr) {
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

        const phoneWarningModal = new bootstrap.Modal(document.getElementById('phoneWarningModal'));
        const phoneWarningForm = document.getElementById('phone-warning-form');
        const phoneInput = document.getElementById('phone_tunisian');
        const errorMessage = document.getElementById('phone-error-message');

        @if(!auth()->user()->phone)
            phoneWarningModal.show();
        @endif
        phoneWarningForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const phoneNumber = phoneInput.value.trim();
        const phoneRegex = /^[2359]\d{7}$/; // Starts with 2, 3, 5, or 9, followed by 7 digits

        if (!phoneRegex.test(phoneNumber)) {
            errorMessage.textContent = 'Veuillez entrer un numéro valide de 8 chiffres commençant par 2, 3, 5 ou 9.';
            errorMessage.style.display = 'block';
            return; // Stop submission if invalid
        }
        
        errorMessage.style.display = 'none'; // Hide error message if valid

        // Prepare data for AJAX request
        let formData = new FormData();
        formData.append('phone', phoneNumber);
        formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token

        // AJAX request to update the phone number
        $.ajax({
                url: "{{ route('update_store_profil_client_phone') }}", // Using the same route as your profile update
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    // On success, hide the modal and reload the page to reflect changes
                    phoneWarningModal.hide();
                    Toastify({
                        text: "Numéro de téléphone enregistré avec succès !",
                        duration: 3000,
                        backgroundColor: "green",
                    }).showToast();
                    
                    // Reload the page after a short delay
                    setTimeout(() => location.reload(), 1500);
                },
                error: function (xhr) {
                    // Display a generic error message
                    errorMessage.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                    errorMessage.style.display = 'block';
                }
            });
        });
    });
</script>

@yield('script')
