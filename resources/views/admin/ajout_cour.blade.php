@extends('layout.master')
@section('title', 'Créer un nouveau cours')
@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/filepond/filepond.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/filepond/image-preview.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/toastify/toastify.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/select/select2.min.css')}}">
<style>
/* === Modern Toggle Switch CSS === */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc; /* Couleur pour "désactivé" */
    -webkit-transition: .4s;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #F8994F; /* Votre couleur pour "activé" */
}

input:focus + .slider {
    box-shadow: 0 0 1px #F8994F;
}

input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}


    .wizard {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        width: 100%;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .circles {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        z-index: 1;
    }

    .bar {
        flex: 1; /* Take up remaining space between circles */
        height: 4px;
        background-color: #ccc;
        position: relative;
        margin: 0 10px; /* Add some spacing between the bar and circles */
    }

    .bar .filled {
        height: 100%;
        background-color: #f8b26a;
        transition: width 0.3s ease;
    }

    .step.active .circles {
        background-color: #f8b26a;
    }

    .is-invalid {
        border-color: red !important;
    }

    .form-section {
        display: flex;
        gap: 20px;
    }

    .form-section .left-section {
        flex: 9; /* col-lg-9 */
    }

    .form-section .right-section {
        flex: 3; /* col-lg-3 */
        border-left: 1px solid #efefef; /* Add a left border */
        border-radius: 20px; /* Add border radius */
        padding-left: 10px; /* Add some padding to separate content from the border */
    }

    .full-width-form {
        width: 100%;
    }
    .accordion-button {
            display: flex;
            justify-content: space-between;
            align-items: center;
    }
    .add-chapter-btn {
        margin-top: 20px;
    }
    #handleList .row.accordion.app-accordion {
        margin-bottom: 10px;
    }

    #certificaList .row.accordion.app-accordion {
        margin-bottom: 10px;
    }
    /* Center the loading spinner */
    #loadingSpinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }

    /* Add a semi-transparent overlay */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white */
        z-index: 999;
        display: none;
    }

    /* Spinner animation */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.25em;
    }
    .filepond-2.filepond--root {
        border-radius: 10% !important;
        height: 170px!important;
    }
    .step.disabled {
        pointer-events: none;
    }
    @media (max-width: 992px) {
        .wizard {
            flex-direction: column;
            align-items: center;
        }

        .bar {
            width: 4px;
            height: 30px;
            margin: 10px 0;
        }

        .step {
            margin-bottom: 20px;
        }
    }
    .btn {
        padding:9px 31px !important;
    }
    .link-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .link-row .form-control {
        flex: 1;
    }
    .link-row .btn-danger {
        padding: 5px 10px !important;
        line-height: 1;
    }
    .link-row .form-control::placeholder {
        color: black !important;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('main-content')
    <div class="container-fluid">
        <!-- Blank start -->
        <div class="row">
            <!-- Default Card start -->
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Wizard and Next Button Row -->
                        <div class="row mb-4">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-8">
                                <div class="wizard">
                                    <div class="step active" data-step="1">
                                        <div class="circles">1</div>
                                    </div>
                                    <div class="bar">
                                        <div class="filled"></div>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="circles">2</div>
                                    </div>
                                    <div class="bar">
                                        <div class="filled"></div>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="circles">3</div>
                                    </div>
                                    <div class="bar">
                                        <div class="filled"></div>
                                    </div>
                                    <div class="step" data-step="4">
                                        <div class="circles">4</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-2">
                                <button id="nextButton" class="col-lg-12 btn btn-primary next"  style="padding:9px 31px !important;"type="button">
                                    <span id="buttonText">Next</span>
                                </button>
                            </div>
                        </div>

                        <!-- Form Sections -->
                            <div id="loadingSpinner" class="d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div id="loadingOverlay"></div>
                            <!-- Step 1 -->
                            <div class="wizard-step form-section" id="step-1">
                                @include('admin.form.ajout_update_cours')
                            </div>

                            <!-- Step 2 -->
                            <div class="wizard-step d-none full-width-form" id="step-2">
                                <div class="app-color-toast app-toast-success chapitres-toast d-none" style="width:100% !important;">
                                    <div class="toast-item">
                                      <div class="toast-icon text-success"></div>
                                      <div class="toast-title text-success">
                                        <i class="ti ti-award f-s-22"></i><span id="toastMessageLesspnCreated">Chapitre créé avec succès.</span>
                                      </div>
                                      <div class="toast-line"></div>
                                    </div>
                                    <div class="toast-close"><i class="fa fa-close text-success"></i></div>
                                </div>
                                <div class="app-color-toast app-toast-success lesson-toast d-none" style="width:100% !important;">
                                    <div class="toast-item">
                                      <div class="toast-icon text-success"></div>
                                      <div class="toast-title text-success">
                                        <i class="ti ti-award f-s-22"></i> Lesson créé avec succès.
                                      </div>
                                      <div class="toast-line"></div>
                                    </div>
                                    <div class="toast-close"><i class="fa fa-close text-success"></i></div>
                                </div>
                               @include('admin.form.ajout_update_chapitres')
                            </div>

                            <!-- Step 3 -->
                            <div class="wizard-step d-none full-width-form" id="step-3">
                                <div class="app-color-toast app-toast-success certifica-toast d-none" style="width:100% !important;">
                                    <div class="toast-item">
                                      <div class="toast-icon text-success"></div>
                                      <div class="toast-title text-success">
                                        <i class="ti ti-award f-s-22"></i><span id="toastMessageLesspnCreated">Certifica créé avec succès.</span>
                                      </div>
                                      <div class="toast-line"></div>
                                    </div>
                                    <div class="toast-close"><i class="fa fa-close text-success"></i></div>
                                </div>
                                <div class="app-color-toast app-toast-success test-toast d-none" style="width:100% !important;">
                                    <div class="toast-item">
                                      <div class="toast-icon text-success"></div>
                                      <div class="toast-title text-success">
                                        <i class="ti ti-award f-s-22"></i><span id="toastMessageLesspnCreated">Test créé avec succès.</span>
                                      </div>
                                      <div class="toast-line"></div>
                                    </div>
                                    <div class="toast-close"><i class="fa fa-close text-success"></i></div>
                                </div>
                                @include('admin.form.ajout_update_certifica')
                            </div>

                             <!-- Step 3 -->
                             <div class="wizard-step d-none full-width-form d-flex flex-column justify-content-end align-items-center" id="step-4">
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Default Card end -->
        </div>
        <!-- Modal for Adding New Chapter -->

            <!-- Modal for Adding New Lesson & Projet -->
            @include('admin.modals.modal_lessons')

            <!-- Modal for Adding New Certifica & tests -->
            @include('admin.modals.modal_certifica')

            <!-- Modal for Adding New Chapter -->
            @include('admin.modals.modal_chapitre')
        <!-- Blank end -->
    </div>
@endsection

@section('script')
    <!-- filepond -->
    <script src="{{asset('assets/vendor/filepond/file-encode.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/validate-size.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/validate-type.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/exif-orientation.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/image-preview.min.js')}}"></script>
    <script src="{{asset('assets/vendor/filepond/filepond.min.js')}}"></script>

    <script src="{{asset('assets/vendor/notifications/toastify-js.js')}}"></script>
    <script src="{{asset('assets/vendor/toastify/toastify.js')}}"></script>

    <!-- phosphor js -->
    <script src="{{asset('assets/vendor/phosphor/phosphor.js')}}"></script>
    <script src="{{asset('assets/vendor/sortable/Sortable.min.js')}}"></script>

    <!--js  
    <script src="{{asset('assets/js/draggable_updated.js')}}"></script>
     js -->

    <script src="{{asset('assets/js/new/cours.js')}}"></script>
    
    <!-- select2 -->
    <script src="{{asset('assets/vendor/select/select2.min.js')}}"></script>

    <!--js-->
    <script src="{{asset('assets/js/select.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Function to show loading spinner and overlay
            function showLoading() {
                const loadingSpinner = document.getElementById('loadingSpinner');
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingSpinner) {
                    loadingSpinner.classList.remove('d-none');
                }
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'block';
                }
            }

            // Function to hide loading spinner and overlay
            function hideLoading() {
                const loadingSpinner = document.getElementById('loadingSpinner');
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingSpinner) {
                    loadingSpinner.classList.add('d-none');
                }
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }

            // Function to show toast notification
            function showToast(message, type) {
                const toastId = `toast-${Date.now()}`;
                const toastContainer = document.getElementById('toastContainer');
                if (!toastContainer) return;

                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }

            function reorderChapitres(movedChapterId, newOrder) {
                console.log("Moved ID:", movedChapterId);
                console.log("New Order:", newOrder);

                // Example: send to backend
                fetch('/chapitres/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        moved_chapter_id: movedChapterId,
                        new_order: newOrder
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Reorder success:', data);
                })
                .catch(error => {
                    console.error('Reorder failed:', error);
                });
            }


            // Initialize Sortable.js on #handleList
            const chapitreList = document.getElementById('handleList');
            if (chapitreList) {
                console.log('verif 1');
                const sortableInstance = new Sortable(chapitreList, {
                    handle: '.list-handle',
                    animation: 150,
                    onSort: (evt) => {
                        console.log('verif sort');
                        const oldIndex = evt.oldIndex;
                        const newIndex = evt.newIndex;

                        if (oldIndex !== newIndex) {
                            const items = chapitreList.children;
                            const chapitreIdMoved = items[newIndex].getAttribute('data-chapter-id'); // dragged item now in newIndex

                            // New ordered list of chapter IDs
                            const newOrder = Array.from(items).map(item => item.getAttribute('data-chapter-id'));

                            // Update hidden input
                            const chapitreOrderInput = document.getElementById('chapitre_order');
                            if (chapitreOrderInput) {
                                chapitreOrderInput.value = newOrder.join(',');
                            }

                            // Send to reorder function or backend
                            reorderChapitres(chapitreIdMoved, newOrder);
                        }
                    }

                });
            }

            // Prevent accordion interference
            document.querySelectorAll('.list-handle').forEach(handle => {
                console.log('verif event');
                handle.addEventListener('click', (e) => e.stopPropagation());
                handle.addEventListener('mousedown', (e) => e.stopPropagation());
            });

        });
    </script>
@endsection