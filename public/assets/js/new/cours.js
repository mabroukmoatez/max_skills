//steps functions
let currentStep = 1;
let courseId = document.getElementById('course_id').value;
const steps = document.querySelectorAll('.wizard-step');
const wizardSteps = document.querySelectorAll('.step');
let lessonImage, lessonVideo, lessonSourceFiles;

// Initialize FilePond
document.addEventListener('DOMContentLoaded', function () {
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileEncode,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType
    );

    const pond = FilePond.create(document.querySelector('input[name="file"]'), {
        allowMultiple: false, // Allow only one file
        acceptedFileTypes: ['image/*', 'video/*'], // Accept only images and videos
        maxFileSize: '10MB', // Limit file size
        labelIdle: 'Glissez-déposez votre fichier ou <span class="filepond--label-action">Parcourir</span>',
        allowFileTypeValidation: true,
        allowFileSizeValidation: true,
    });
    window.pond = pond;
    $('.select2-icon').select2({
        width: "100%",
        minimumResultsForSearch: Infinity,
        dropdownCssClass: "form-control", 
        containerCssClass: "form-control", 
        templateSelection: formatText,
        templateResult: formatText
    });

    const idCour = getUrlParameter('id_cour');
    if (idCour) {
        fetchCourseData(idCour);
    }

 
    showStep(1);

    const addChapterBtn = document.getElementById('addChapterBtn');
    const addChapterModal = new bootstrap.Modal(document.getElementById('addChapterModal'));

    const publishButton = document.getElementById('publishChapterBtn');
    const chapterForm = document.getElementById('chapterForm');
    const loadingSpinner = document.getElementById('loadingSpinnerChapitreModal');
    const loadingSpinnerCertifica = document.getElementById('loadingSpinnerCertificaModal');

    const pondBanner = FilePond.create(document.querySelector('#path_banner'), {
        allowMultiple: false, // Allow only one file
        acceptedFileTypes: ['image/*'], // Accept only images
        maxFileSize: '10MB', // Limit file size
        labelIdle: 'Glissez-déposez votre fichier ou <span class="filepond--label-action">Parcourir</span>',
        allowFileTypeValidation: true,
        allowFileSizeValidation: true,
    });

    const pondBannerCertifica = FilePond.create(document.querySelector('#path_banner_certifica'), {
        allowMultiple: false, // Allow only one file
        acceptedFileTypes: ['image/*'], // Accept only images
        maxFileSize: '10MB', // Limit file size
        labelIdle: 'Glissez-déposez votre fichier ou <span class="filepond--label-action">Parcourir</span>',
        allowFileTypeValidation: true,
        allowFileSizeValidation: true,
    });

    window.pondBanner = pondBanner;
    window.pondBannerCertifica = pondBannerCertifica;

    addChapterBtn.addEventListener('click', function () {
        document.getElementById('id_chapitre').value = null;
        document.getElementById('chapterTitle').value = null;
        document.getElementById('chapterDescription').value = null;
        
        if(window.pondBanner)
        {
            window.pondBanner.removeFiles();
        }
        if(window.pondResume)
        {
            window.pondResume.removeFiles();
        }
        const pathResumeLabel = document.querySelector('label[for="path_resume"]');
        if (pathResumeLabel) {
            pathResumeLabel.removeAttribute('click');
            pathResumeLabel.style.cursor = ''; 
            pathResumeLabel.style.color = '';
            pathResumeLabel.style.textDecoration = '';
        }
        document.getElementById('timer_hours_chapitre').value = null;
        document.getElementById('timer_minutes_chapitre').value = null;
        document.getElementById('timer_seconds_chapitre').value = null;
        document.getElementById('publishChapterBtn').textContent = 'Publier';
        document.getElementById('deleteChapitreBtn').textContent = 'Annuler';

        document.getElementById('deleteChapitreBtn').classList.remove('btn-danger');
        document.getElementById('deleteChapitreBtn').classList.add('btn-secondary');

        document.getElementById('deleteChapitreBtn').removeAttribute('onclick');
        document.getElementById('deleteChapitreBtn').setAttribute('data-bs-dismiss','modal');
        document.getElementById('deleteChapitreBtn').setAttribute('onclick','closeModalChapitre()');
        addChapterModal.show();
        document.getElementById('cour_id').value = document.getElementById('course_id').value;
    });

    publishButton.addEventListener('click', function () {
        const requiredInputs = [
            { id: 'chapterTitle', message: 'Le titre du chapitre est requis.' },
            { id: 'chapterDescription', message: 'La description du chapitre est requise.' },
            { id: 'timer_hours_chapitre', message: 'Les heures du timer sont requises.' },
            { id: 'timer_minutes_chapitre', message: 'Les minutes du timer sont requises.' },
            { id: 'timer_seconds_chapitre', message: 'Les secondes du timer sont requises.' },
        ];

        const bannerFiles = pondBanner.getFiles();
        if (bannerFiles.length === 0) {
            alert('Veuillez télécharger une bannière pour le chapitre.');
            return;
        }

        // Validate required inputs
        let isValid = true;
        requiredInputs.forEach(input => {
            const field = document.getElementById(input.id);
            if (!field || !field.value.trim()) {
                isValid = false;
                // Add red border to the missing input
                field.style.border = '1px solid red';
                // Optionally, display an error message below the input
                const errorMessage = document.createElement('div');
                errorMessage.style.color = 'red';
                errorMessage.style.fontSize = '0.875rem';
                errorMessage.style.marginTop = '0.25rem';
                errorMessage.innerText = input.message;
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            } else {
                // Remove red border if the input is valid
                field.style.border = '';
                // Remove any existing error message
                const existingErrorMessage = field.nextElementSibling; // Use nextElementSibling
                if (existingErrorMessage && existingErrorMessage.style.color === 'red') {
                    existingErrorMessage.remove();
                }
            }
        });

        // Stop if validation fails
        if (!isValid) {
            return;
        }

        // Disable all form inputs
        const formInputs = chapterForm.querySelectorAll('input, textarea, button, select');
        formInputs.forEach(input => {
            input.disabled = true;
        });

        // Show loading spinner
        loadingSpinner.classList.remove('d-none');

        // Submit the form data using Fetch API
        const formData = new FormData(chapterForm);
        formData.append('cour_id', document.getElementById('cour_id').value);
        formData.append('id_chapitre', document.getElementById('id_chapitre').value);
        formData.append('title', document.getElementById('chapterTitle').value);
        formData.append('description', document.getElementById('chapterDescription').value);
        formData.append('timer_hours_chapitre', document.getElementById('timer_hours_chapitre').value);
        formData.append('timer_minutes_chapitre', document.getElementById('timer_minutes_chapitre').value);
        formData.append('timer_seconds_chapitre', document.getElementById('timer_seconds_chapitre').value);
        formData.append('type','chapitre');
        // Append the banner file
        if (bannerFiles.length > 0) {
            formData.append('path_banner', bannerFiles[0].file);
        }

        // Append the resume video file (if required)
        const resumeFiles = pondResume.getFiles();
        if (resumeFiles.length > 0) {
            formData.append('path_resume', resumeFiles[0].file);
        }

        // Send the request
        fetch('/admin/ajout_chapitre', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    handleToastModalSucceChapitre();
                    addChapterModal.hide();
                    chapterForm.reset();
                    if (pondBanner) {
                        pondBanner.removeFiles();
                    }
                    if (pondResume) {
                        pondResume.removeFiles();
                    }
                    const pathResumeLabel = document.querySelector('label[for="path_resume"]');
                    if (pathResumeLabel) {
                        pathResumeLabel.removeAttribute('click');
                        pathResumeLabel.style.cursor = ''; 
                        pathResumeLabel.style.color = '';
                        pathResumeLabel.style.textDecoration = '';
                    }
                    const chapterId = data.chapter.id;
                    const existingLi = document.querySelector(`li[data-chapter-id="${chapterId}"]`);
                    if (existingLi) {
                        const chapterTitle = existingLi.querySelector('.chapter-title');
                        chapterTitle.textContent = data.chapter.title;
                        chapterTitle.setAttribute('onclick', `getChapitreData('${data.chapter.id}')`);
                    } else {
                    const newLi = document.createElement('li');
                    newLi.className = 'row accordion app-accordion accordion-light-secondary';
                    newLi.setAttribute('data-chapter-id', chapterId);
                    newLi.innerHTML = `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-${data.chapter.id}" aria-expanded="false"
                                        aria-controls="flush-collapse-${data.chapter.id}">
                                    <i class="ti ti-grip-vertical list-handle"></i>
                                    <span class="chapter-title" onclick="getChapitreData('${data.chapter.id}')">${data.chapter.title}</title>
                                </button>
                            </h2>
                            <div id="flush-collapse-${data.chapter.id}" class="accordion-collapse collapse"
                                data-bs-parent="#accordion1">
                                <div class="accordion-body">
                                    <ul style="color:#000;margin-left:25px;" id="list-lessons-${data.chapter.id}">
                                    
                                    </ul>
                                    <div class="row">
                                        <div class="col-auto btn1">
                                        <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="openAddLessonModal('${data.chapter.id}','','typelesson')">
                                                <i class="ti ti-file-plus"></i>
                                                Leçon
                                            </button>
                                        </div>
                                        <div class="col-auto btn2">
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="openAddLessonModal('${data.chapter.id}','typeprojet')">
                                                <i class="ti ti-file-zip"></i>
                                                Project
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    const handleList = document.getElementById('handleList');
                    handleList.appendChild(newLi);
                    }
                } else {
                    console.error('Validation errors:', data.errors);
                    alert('Validation errors occurred. Please check the form.');
                }
            })
            .catch(error => {
                // Handle error
                console.error('Error:', error);
                alert('Une erreur s\'est produite lors de la création du chapitre.');
            })
            .finally(() => {
                // Re-enable form inputs and hide loading spinner
                formInputs.forEach(input => {
                    input.disabled = false;
                });
                loadingSpinner.classList.add('d-none');
                addChapterModal.hide();
            });
    });

    const publishCertificaBtn = document.getElementById('publishCertificaBtn');
    publishCertificaBtn.textContent = 'Publier';

    publishCertificaBtn.addEventListener('click', function () {
        const requiredInputs = [
            { id: 'certificaTitle', message: 'Le titre du chapitre est requis.' },
            { id: 'certificaDescription', message: 'La description du chapitre est requise.' },
        ];
        
        const bannerCertificaFiles = window.pondBannerCertifica.getFiles();

        if (bannerCertificaFiles.length === 0) {
            alert('Veuillez télécharger une bannière pour le chapitre.');
            return;
        }

        // Validate required inputs
        let isValid = true;
        requiredInputs.forEach(input => {
            const field = document.getElementById(input.id);
        
            if (!field) {
                console.error(`Element with id "${input.id}" not found.`); // Debugging
                return; // Skip this input if the element doesn't exist
            }
        
            if (!field.value.trim()) {
                isValid = false;
                // Add red border to the missing input
                field.style.border = '1px solid red';
                // Optionally, display an error message below the input
                const errorMessage = document.createElement('div');
                errorMessage.style.color = 'red';
                errorMessage.style.fontSize = '0.875rem';
                errorMessage.style.marginTop = '0.25rem';
                errorMessage.innerText = input.message;
                field.parentNode.insertBefore(errorMessage, field.nextSibling);
            } else {
                // Remove red border if the input is valid
                field.style.border = '';
                // Remove any existing error message
                const existingErrorMessage = field.nextElementSibling;
                if (existingErrorMessage && existingErrorMessage.style.color === 'red') {
                    existingErrorMessage.remove();
                }
            }
        });

        // Stop if validation fails
        if (!isValid) {
            return;
        }

        // Disable all form inputs
        const formInputs = chapterForm.querySelectorAll('input, textarea, button');
        formInputs.forEach(input => {
            input.disabled = true;
        });

        // Show loading spinner
        loadingSpinnerCertifica.classList.remove('d-none');

        // Submit the form data using Fetch API
        const formData = new FormData(chapterForm);
        formData.append('cour_id', document.getElementById('cour_id').value);
        formData.append('id_chapitre', document.getElementById('id_chapitre').value);
        formData.append('title', document.getElementById('certificaTitle').value);
        formData.append('description', document.getElementById('certificaDescription').value);
        formData.append('timer_hours_chapitre', document.getElementById('timer_hours_certifica').value);
        formData.append('timer_minutes_chapitre', document.getElementById('timer_minutes_certifica').value);
        formData.append('timer_seconds_chapitre', document.getElementById('timer_seconds_certifica').value);
        formData.append('type','certifica');
        // Append the banner file
        if (bannerCertificaFiles.length > 0) {
            formData.append('path_banner', bannerCertificaFiles[0].file);
        }

        // Send the request
            fetch('/admin/ajout_chapitre', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    handleToastModalSucceCertifica();
                    closeModalCertifica();
                    const chapterId = data.chapter.id;
                    const existingLi = document.querySelector(`li[data-certifica-id="${chapterId}"]`);
                    if (existingLi) {
                        const chapterTitle = existingLi.querySelector('.chapter-title');
                        chapterTitle.textContent = data.chapter.title;
                        chapterTitle.setAttribute('onclick', `getCertificaData('${data.chapter.id}','${data.chapter.title}','${data.chapter.description}','${data.chapter.path_banner}','${data.chapter.path_resume}','${data.chapter.timer_hours}','${data.chapter.timer_minutes}','${data.chapter.timer_seconds}')`);
                    } else {
                    const newLi = document.createElement('li');
                    newLi.className = 'row accordion app-accordion accordion-light-secondary';
                    newLi.setAttribute('data-certifica-id', chapterId);
                    newLi.innerHTML = `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-${data.chapter.id}" aria-expanded="false"
                                        aria-controls="flush-collapse-${data.chapter.id}">
                                    <i class="ti ti-grip-vertical list-handle"></i>
                                    <span class="chapter-title" onclick="getCertificaData('${data.chapter.id}','${data.chapter.title}','${data.chapter.description}','${data.chapter.path_banner}','${data.chapter.path_resume}','${data.chapter.timer_hours}','${data.chapter.timer_minutes}','${data.chapter.timer_seconds}')">${data.chapter.title}</title>
                                </button>
                            </h2>
                            <div id="flush-collapse-${data.chapter.id}" class="accordion-collapse collapse"
                                data-bs-parent="#accordion1">
                                <div class="accordion-body">
                                    <ul style="color:#000;margin-left:25px;" id="list-lessons-${data.chapter.id}">
                                    
                                    </ul>
                                    <div class="row">
                                        <div class="col-auto btn1">
                                        <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="openAddLessonModal('${data.chapter.id}','','typetest')">
                                                <i class="ti ti-file-pencil"></i>
                                                Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    const handleList = document.getElementById('certificaList');
                    handleList.appendChild(newLi);
                    }
                } else {
                    console.error('Validation errors:', data.errors);
                    alert('Validation errors occurred. Please check the form.');
                }
            })
            .catch(error => {
                // Handle error
                console.error('Error:', error);
                alert('Une erreur s\'est produite lors de la création du chapitre.');
            })
            .finally(() => {
                // Re-enable form inputs and hide loading spinner
                formInputs.forEach(input => {
                    input.disabled = false;
                });
                loadingSpinnerCertifica.classList.add('d-none');
                closeModalCertifica();
            });
    });
    wizardSteps.forEach(step => {
        step.addEventListener('click', async function () {
            const stepNumber = parseInt(this.getAttribute('data-step'));
            if (stepNumber > currentStep && !validateStep(currentStep)) {
                alert('Please fill out all required fields before proceeding.');
                return;
            }
            const formData = new FormData();
            formData.append('course_id', document.getElementById('course_id').value);
            formData.append('title', document.getElementById('title').value);
            formData.append('keyword', document.getElementById('keyword').value);
            formData.append('top_bar', document.getElementById('top_bar').value);
            formData.append('button', document.getElementById('button').value);
            formData.append('price_init', document.getElementById('price_init').value);
            formData.append('price_promo', document.getElementById('price_promo').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('visibility', document.getElementById('visibility').value);
    
            pond.getFiles().forEach(file => {
                formData.append('file', file.file);
            });
    
            // Only call saveCourseData if we're moving from step 1 to step 2
            if (currentStep === 1) {
                await saveCourseData(formData);
            }
            // Move to the selected step without saving course data
            showStep(stepNumber);
        });
    });
    const lessonImageInput = document.querySelector('#lessonImage');
    if (lessonImageInput) {
        window.lessonImage = FilePond.create(lessonImageInput, {
            allowMultiple: false,
            acceptedFileTypes: ['image/*'],
            maxFileSize: '10MB',
            labelIdle: `<div class="filepond--label-action text-decoration-none">Ajouter votre image.</div>`,
        });
    }

    const lessonVideoInput = document.querySelector('#lessonVideo');
    if (lessonVideoInput) {
        window.lessonVideo = FilePond.create(lessonVideoInput, {
            allowMultiple: false,
            acceptedFileTypes: ['video/*'],
            maxFileSize: '2048MB',
            labelIdle: `<div class="filepond--label-action text-decoration-none">Ajouter votre Vidéo.</div>`,
        });
    }

    const lessonSourceFilesInput = document.querySelector('#lessonSourceFiles');
    if (lessonSourceFilesInput) {
        window.lessonSourceFiles = FilePond.create(lessonSourceFilesInput, {
            allowMultiple: false,
            acceptedFileTypes: ['application/zip', '.zip'],
            maxFileSize: '2000MB',
            labelIdle: `<div class="filepond--label-action text-decoration-none">Ajouter votre Fichier Source.</div>`,
        });
    }
    const linksContainer = document.getElementById('lesson-links-container');
    const addLessonModal = document.getElementById('addLessonModal');
  const addLinkBtn = document.getElementById('add-lesson-link-btn');
    if (addLinkBtn) {
        // When the button is clicked, call createLinkRow with empty values.
        addLinkBtn.addEventListener('click', () => createLinkRow('', ''));
    }
    addLessonModal.addEventListener('hidden.bs.modal', function () {
        const lessonFormInput = document.getElementById('lessonForm');
        lessonFormInput.reset();
        document.getElementById('idLesson').value = "";
        linksContainer.innerHTML = ''; 
        if (window.lessonImage) {
            window.lessonImage.removeFiles();
        }
        if (window.lessonVideo) {
            window.lessonVideo.removeFiles();
        }
        if (window.lessonSourceFiles) {
            window.lessonSourceFiles.removeFiles();
        }
    });



    
});

function formatText (icon) {
    return $('<span><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
};



function addEventCertifica(){
    console.log('added event certif btn');

}

function openCertificaModal(){
    const addCertificaModal = new bootstrap.Modal(document.getElementById('addCertificaModal'));
    document.getElementById('id_chapitre').value = null;
    document.getElementById('certificaTitle').value = null;
    document.getElementById('certificaDescription').value = null;
    const deleteCertificaBtn = document.getElementById('deleteCertificaBtn');
    
    const publishCertificaBtn = document.getElementById('publishCertificaBtn');
    publishCertificaBtn.textContent = 'Publier';

    deleteCertificaBtn.textContent = 'Annuler';
    deleteCertificaBtn.classList.remove('btn-danger');
    deleteCertificaBtn.classList.add('btn-secondary');
    deleteCertificaBtn.removeAttribute('onclick');

    const pondBannerCertifica = FilePond.create(document.querySelector('#path_banner_certifica'), {
        allowMultiple: false,
        acceptedFileTypes: ['image/*'],
        maxFileSize: '10MB',
        labelIdle: 'Glissez-déposez votre fichier ou <span class="filepond--label-action">Parcourir</span>',
        allowFileTypeValidation: true,
        allowFileSizeValidation: true,
    });
    window.pondBannerCertifica = pondBannerCertifica;
    if(window.pondBannerCertifica.getFiles()){
        window.pondBannerCertifica.removeFiles();
    }
    
    addCertificaModal.show();
}

function updateProgressBar(currentStep) {
    const bars = document.querySelectorAll('.bar .filled');

    // Reset all bars to grey
    bars.forEach(bar => {
        bar.style.width = '0%';
        bar.style.backgroundColor = '#ccc';
    });
    // Fill the bars up to the current step
    if (currentStep >= 2) {
        bars[0].style.width = '100%';
        bars[0].style.backgroundColor = '#f8b26a';
    }
    if (currentStep >= 3) {
        bars[0].style.width = '100%';
        bars[0].style.backgroundColor = '#f8b26a';
        bars[1].style.width = '100%';
        bars[1].style.backgroundColor = '#f8b26a';
    }
    if (currentStep >= 4) {
        bars[0].style.width = '100%';
        bars[0].style.backgroundColor = '#f8b26a';
        bars[1].style.width = '100%';
        bars[1].style.backgroundColor = '#f8b26a';
        bars[2].style.width = '100%';
        bars[2].style.backgroundColor = '#f8b26a';
    }
}

//Chapitre JS Function
const handleToastModalSucceChapitre = () => {
    $("#step-2 .app-color-toast.app-toast-success.chapitres-toast").removeClass('d-none');
    setTimeout(function () {
        $("#step-2 .app-color-toast.app-toast-success.chapitres-toast").addClass("d-none");
    }, 5000);
}

const handleToastModalSucceCertifica = () => {
    $("#step-2 .app-color-toast.app-toast-success.certifica-toast").removeClass('d-none');
    setTimeout(function () {
        $("#step-2 .app-color-toast.app-toast-success.certifica-toast").addClass("d-none");
    }, 5000);
}

function deleteChapitre(chapterId,title) {
    // Show the confirmation modal
    const deleteModal = new bootstrap.Modal(document.getElementById('ModalDeleteChapitreConfirm'));
    deleteModal.show();

    document.getElementById('chapterTitleInModal').textContent = title;
    // Update the modal title and content with the chapter title
    const modalTitle = document.querySelector('#ModalDeleteChapitreConfirm .modal-title');
    const modalBody = document.querySelector('#ModalDeleteChapitreConfirm .modal-body h5');
    modalTitle.textContent = `Suppression du chapitre`;
    modalBody.textContent = `Êtes-vous sûr de vouloir supprimer le chapitre "${title}" ?`;

    // Add event listener to the "Confirmer" button
    const confirmButton = document.querySelector('#ModalDeleteChapitreConfirm .modal-footer .btn-light-primary');
    confirmButton.onclick = () => {
        // Send a DELETE request to the server
        fetch(`/admin/delete_chapitre/${chapterId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chapterLi = document.querySelector(`li[data-chapter-id="${chapterId}"]`);
                const testLi = document.querySelector(`li[data-certifica-id="${chapterId}"]`);
                if (chapterLi) {
                    chapterLi.remove();
                    closeModalChapitre();
                    alert('Chapitre supprimé avec succès !');
                } 
                if(testLi) {
                    testLi.remove();
                    closeModalCertifica();
                    alert('Certifica supprimé avec succès !');
                }
            } else {
                alert('Une erreur s\'est produite lors de la suppression du chapitre.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur s\'est produite lors de la suppression du chapitre.');
        })
        .finally(() => {
            deleteModal.hide();
        });
    };
}

function deleteLessonProjet(lessonId, title, type) {
    // 1. Get the confirmation modal instance
    const deleteModalElement = document.getElementById('ModalDeleteLessonProjetConfirm');
    if (!deleteModalElement) {
        console.error('Delete confirmation modal not found!');
        return;
    }
    const deleteModal = new bootstrap.Modal(deleteModalElement);

    // 2. Update modal content with the specific item details
    const modalTitle = deleteModalElement.querySelector('.modal-title');
    const modalBodyText = deleteModalElement.querySelector('#lessonProjetTitleInModal');
    
    modalTitle.textContent = `Suppression du ${type}`;
    modalBodyText.textContent = `"${title}"`;

    // 3. Handle the confirmation button click
    const confirmButton = deleteModalElement.querySelector('#confirmDeleteLessonBtn');
    
    // IMPORTANT: To prevent multiple event listeners from stacking up if the modal is opened several times,
    // we replace the button with a clone of itself. This effectively removes all prior event listeners.
    const newConfirmButton = confirmButton.cloneNode(true);
    confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

    newConfirmButton.addEventListener('click', () => {
        // Disable button to prevent double-clicking
        newConfirmButton.disabled = true;
        newConfirmButton.textContent = 'Suppression...';

        // Send the DELETE request to the server
        fetch(`/admin/delete_lesson_projet/${lessonId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the item from the UI
                const lessonElement = document.querySelector(`li[data-lesson-id="${lessonId}"]`);
                const testElement = document.querySelector(`li[data-test-id="${lessonId}"]`);
                
                if (lessonElement) lessonElement.remove();
                if (testElement) testElement.remove();
                
                // Show success feedback (using Toastify for a better UX than alert)
                Toastify({
                    text: `${type} "${title}" a été supprimé avec succès.`,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50",
                }).showToast();

                deleteModal.hide();
            } else {
                // Show error from backend
                alert(`Erreur: ${data.message || 'Une erreur s\'est produite lors de la suppression.'}`);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert('Une erreur réseau est survenue.');
        })
        .finally(() => {
            // Re-enable the button in case of an error
            newConfirmButton.disabled = false;
            newConfirmButton.textContent = 'Confirmer la Suppression';
        });
    });

    // 4. Finally, show the confirmation modal
    deleteModal.show();
}


function closeModalChapitre() {
    const addChapterModalElement = document.getElementById('addChapterModal');
    if (!addChapterModalElement) {
        console.error('Modal element not found!');
        return;
    }
    const addChapterModal = bootstrap.Modal.getInstance(addChapterModalElement);
    if (!addChapterModal) {
        console.error('Modal instance not found!');
        return;
    }
    addChapterModal.hide();
    addChapterModalElement.classList.remove('show');
    addChapterModalElement.setAttribute('aria-hidden', 'true');
    addChapterModalElement.style.display = 'none';
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.remove();
    }
    document.body.classList.remove('modal-open');
    const chapterForm = document.getElementById('chapterForm');
    if (chapterForm) {
        chapterForm.reset();

    } else {
        console.error('Form element not found!');
    }
    if (window.pondBanner) {
        window.pondBanner.removeFiles();

    } else {
        console.error('FilePond banner instance not found!');
    }
    if (window.pondResume) {
        window.pondResume.removeFiles();
        const pathResumeLabel = document.querySelector('label[for="path_resume"]');
        if (pathResumeLabel) {
            pathResumeLabel.removeAttribute('click');
            pathResumeLabel.style.cursor = ''; 
            pathResumeLabel.style.color = '';
            pathResumeLabel.style.textDecoration = '';
        }
  
    } else {
        console.error('FilePond resume instance not found!');
    }
}
function triggerDeleteConfirmation() {
    // 1. Get the lesson details from the currently open "edit" modal
    const lessonId = document.getElementById('idLesson').value;
    const lessonTitle = document.getElementById('lessonTitle').value;
    const typeModalValue = document.getElementById('typeModal').value;

    // Determine the display name ('Leçon', 'Projet', 'Test')
    let lessonType = 'Élément';
    if (typeModalValue === 'lesson') {
        lessonType = 'Leçon';
    } else if (typeModalValue === 'projet') {
        lessonType = 'Projet';
    } else if (typeModalValue === 'typetest') {
        lessonType = 'Test';
    }

    // 2. Ensure we are in "edit" mode (a lesson ID must exist)
    if (!lessonId) {
        alert("Vous ne pouvez pas supprimer un élément qui n'a pas encore été enregistré.");
        return;
    }

    // 3. Hide the "edit" modal before showing the confirmation modal
    const addLessonModalInstance = bootstrap.Modal.getInstance(document.getElementById('addLessonModal'));
    if (addLessonModalInstance) {
        addLessonModalInstance.hide();
    }

    // 4. Call the main delete function with the retrieved details
    // Use a short timeout to ensure the first modal has finished hiding to avoid UI glitches
    setTimeout(() => {
        deleteLessonProjet(lessonId, lessonTitle, lessonType);
    }, 250); // 250ms delay
}

function closeModalCertifica() {
    const addChapterModalElement = document.getElementById('addCertificaModal');
    if (!addChapterModalElement) {
        console.error('Modal element not found!');
        return;
    }
    const addChapterModal = bootstrap.Modal.getInstance(addChapterModalElement);
    if (!addChapterModal) {
        console.error('Modal instance not found!');
        return;
    }
    addChapterModal.hide();
    addChapterModalElement.classList.remove('show');
    addChapterModalElement.setAttribute('aria-hidden', 'true');
    addChapterModalElement.style.display = 'none';
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.remove();
    }
    document.body.classList.remove('modal-open');
    const chapterForm = document.getElementById('CertificaForm');
    if (chapterForm) {
        chapterForm.reset();

    } else {
        console.error('Form element not found!');
    }
}

function getUrlParameter(name) {
    name = name.replace(/[\[\]]/g, '\\$&');
    const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
    const results = regex.exec(window.location.href);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

async function fetchCourseDetails(courseId) {
    try {
        const response = await fetch(`/admin/get-course-details/${courseId}`);
        const data = await response.json();

        if (data.success) {
            // Update the UI with the fetched data
            updateStep4UI(data.course, data.chapters, data.certifications);
        } else {
            console.error('Error fetching course details:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

//step 4 
function updateStep4UI(course, chapters, certifications) {
    const step4Container = document.getElementById('step-4');

    // Clear existing content
    step4Container.innerHTML = '';

    // Add course details
    const courseHTML = `
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="product-content-box">
                        <div class="product-grid" onclick="window.location.href='/admin/cours'">
                            <div class="product-image left-main-img img-box">
                                <a href="#" class="image">
                                    <img src="${course.path_banner}" alt="${course.title}">
                                    <div class="transparent-box">
                                        <div class="caption">
                                            <i class="fa-solid fa-ban fa-fw"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="d-flex justify-content-between">
                                <span class="d-flex-center b-r-15 f-s-10" style="margin-bottom: 15px !important;background-color: #F8994F!important;color:#fff !important;">
                                    <div style="padding-right: 6px;padding-left: 6px;">
                                        ${course.visibility === 1 ? 'Publié' : course.visibility === 0 ? 'Privé' : 'Archivée'}
                                    </div>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="margin-bottom:15px !important;">
                                <a href="" class="m-0 f-s-15 f-w-400" style="color:#000 !important" title="${course.title}">${course.title}</a>
                            </div>
                            <div class="profile-friends">
                                <div class="d-flex align-items-center">
                                    <div class="h-40 w-40 d-flex-center b-r-50 overflow-hidden bg-dark">
                                        <img src="${course.user.photo ? course.user.photo : '../assets/images/ai_avtar/2.jpg'}" alt="image" class="img-fluid">                                    </div>
                                    <div class="flex-grow-1 ps-2">
                                        <div class="fw-medium">${course.user.firstname} ${course.user.name}</div>
                                        <div class="text-muted f-s-12">${course.user.niveau || ''}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;


    // Combine all HTML and update the UI
    step4Container.innerHTML = courseHTML ;
}
// Function to show a specific step
async function showStep(stepNumber) {
    // Hide all steps
    steps.forEach(step => step.classList.add('d-none'));
    // Show the selected step
    document.querySelector(`#step-${stepNumber}`).classList.remove('d-none');
    updateProgressBar(stepNumber);

    // Update active state in wizard navigation
    wizardSteps.forEach((step, index) => {
        if (index + 1 <= stepNumber) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });

    const nextButton = document.getElementById('nextButton');
    const nextButtonChapitre = document.getElementById('nextButtonChapitre');
    const nextButtonFinish = document.getElementById('nextButtonFinish');
    const nextButtonCertifica = document.getElementById('nextButtonCertifica');

    if (stepNumber === 1) {
        const idCour = getUrlParameter('id_cour');
        let  = document.getElementById('course_id').value;
        if (idCour) {
            fetchCourseData(idCour);
        } else if (typeof cour_id_initial !== 'undefined' && cour_id_initial) {
            fetchCourseData(cour_id_initial);
        } 
        if (nextButton) {
            nextButton.textContent = 'Suivant';
            nextButton.id = 'nextButton';
            nextButton.onclick = nextButtonFunction;
        } else if (nextButtonChapitre) {
            nextButtonChapitre.textContent = 'Suivant';
            nextButtonChapitre.id = 'nextButton';
            nextButtonChapitre.onclick = nextButtonFunction;
        } else if (nextButtonCertifica) {
            nextButtonCertifica.textContent = 'Suivant';
            nextButtonCertifica.id = 'nextButton';
            nextButtonCertifica.onclick = nextButtonFunction; 
        } else if(nextButtonFinish) {
            nextButtonFinish.textContent = 'Suivant';
            nextButtonFinish.id = 'nextButton';
            nextButtonFinish.onclick = nextButtonFunction; 
        }
    } else if (stepNumber === 2) {
        let cour_id_initial = document.getElementById('course_id').value;
        if (document.getElementById('cour_id')) {
            document.getElementById('cour_id').value = cour_id_initial;
        }
        await fetchChapitreData(cour_id_initial);

       
        if (nextButton) {
            nextButton.textContent = 'Suivant';
            nextButton.id = 'nextButtonChapitre';
            nextButton.onclick = nextButtonChapitreFunction;
        } else if (nextButtonChapitre) {
            nextButtonChapitre.textContent = 'Suivant';
            nextButtonChapitre.id = 'nextButtonChapitre';
            nextButtonChapitre.onclick = nextButtonChapitreFunction;
        } else if (nextButtonCertifica) {
            nextButtonCertifica.textContent = 'Suivant';
            nextButtonCertifica.id = 'nextButtonChapitre';
            nextButtonCertifica.onclick = nextButtonChapitreFunction; 
        } else if (nextButtonFinish) {
            nextButtonFinish.textContent = 'Suivant';
            nextButtonFinish.id = 'nextButtonChapitre';
            nextButtonFinish.onclick = nextButtonChapitreFunction; 
        }
    } else if (stepNumber === 3) {
        let cour_id_initial = document.getElementById('course_id').value;
        if (document.getElementById('cour_id')) {
            document.getElementById('cour_id').value = cour_id_initial;
        }

        await fetchCertificaData(cour_id_initial);
        addEventCertifica();
        const pondBannerCertifica = FilePond.create(document.querySelector('#path_banner_certifica'), {
            allowMultiple: false,
            acceptedFileTypes: ['image/*'],
            maxFileSize: '10MB',
            labelIdle: 'Glissez-déposez votre fichier ou <span class="filepond--label-action">Parcourir</span>',
            allowFileTypeValidation: true,
            allowFileSizeValidation: true,
        });
        window.pondBannerCertifica = pondBannerCertifica;
        if (nextButton) {
            nextButton.textContent = 'Suivant';
            nextButton.id = 'nextButtonCertifica';
            nextButton.onclick = nextButtonCertificaFunction;
        } else if (nextButtonChapitre) {
            nextButtonChapitre.textContent = 'Suivant';
            nextButtonChapitre.id = 'nextButtonCertifica';
            nextButtonChapitre.onclick = nextButtonCertificaFunction;
        } else if (nextButtonCertifica) {
            nextButtonCertifica.textContent = 'Suivant';
            nextButtonCertifica.id = 'nextButtonCertifica';
            nextButtonCertifica.onclick = nextButtonCertificaFunction; 
        } else if (nextButtonFinish) {
            nextButtonFinish.textContent = 'Suivant';
            nextButtonFinish.id = 'nextButtonCertifica';
            nextButtonFinish.onclick = nextButtonCertificaFunction; 
        }
    } else if (stepNumber === 4){
        const courseId = document.getElementById('course_id').value;
        await fetchCourseDetails(courseId);
        const wizardSteps = document.querySelectorAll('.step');
        wizardSteps.forEach(step => {
            step.classList.add('disabled');
        });
        if (nextButton) {
            nextButton.textContent = 'Terminer';
            nextButton.id = 'nextButtonFinish';
            nextButton.onclick = nextButtonFinishFunction;
        } else if (nextButtonChapitre) {
            nextButtonChapitre.textContent = 'Terminer';
            nextButtonChapitre.id = 'nextButtonFinish';
            nextButtonChapitre.onclick = nextButtonFinishFunction;
        } else if (nextButtonCertifica) {
            nextButtonCertifica.textContent = 'Terminer';
            nextButtonCertifica.id = 'nextButtonFinish';
            nextButtonCertifica.onclick = nextButtonFinishFunction; 
        } else if (nextButtonFinish) {
            nextButtonFinish.textContent = 'Terminer';
            nextButtonFinish.id = 'nextButtonFinish';
            nextButtonFinish.onclick = nextButtonFinishFunction; 
        }
    }
    currentStep = stepNumber;

}



// Function to validate the current step
function validateStep(stepNumber) {
    const currentForm = document.querySelector(`#step-${stepNumber}`);
    const requiredInputs = currentForm.querySelectorAll('.wizard-required');

    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else if (input.hasAttribute('minlength')) {
            const minLength = parseInt(input.getAttribute('minlength'));
            if (input.value.trim().length < minLength) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        } else {
            input.classList.remove('is-invalid');
        }
    });
    if (stepNumber == 1) {
        titleInput = document.getElementById('title');
        if (titleInput && titleInput.value.trim().length < 20) {
            isValid = false;
            titleInput.classList.add('is-invalid');
        } else {
            titleInput.classList.remove('is-invalid');
        }
    }
    return isValid;
}

// Function to disable all form inputs
function disableFormInputs() {
    const inputs = document.querySelectorAll('#wizardForm input, #wizardForm select, #wizardForm textarea, #wizardForm button');
    inputs.forEach(input => {
        input.disabled = true;
    });

    const nextButton = document.getElementById('nextButton');
    const nextButtonChapitre = document.getElementById('nextButtonChapitre');
    const nextButtonFinish = document.getElementById('nextButtonFinish');

    // Enable the next button
    if (nextButton) {
        nextButton.disabled = true;
    }
    if (nextButtonChapitre) {
        nextButtonChapitre.disabled = true;
    }
    if (nextButtonFinish) {
        nextButtonFinish.disabled = true;
    }

    const loadingSpinner = document.getElementById('loadingSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (loadingSpinner) {
        loadingSpinner.classList.remove('d-none');
    }
    if (loadingOverlay) {
        loadingOverlay.style.display = 'block';
    }

    // Update button text if it exists
    const buttonText = document.getElementById('buttonText');
    if (buttonText) {
        buttonText.textContent = 'Loading...';
    }
}

// Function to enable all form inputs
function enableFormInputs() {
    const inputs = document.querySelectorAll('#wizardForm input, #wizardForm select, #wizardForm textarea, #wizardForm button');
    inputs.forEach(input => {
        input.disabled = false;
    });

    const nextButton = document.getElementById('nextButton');
    const nextButtonChapitre = document.getElementById('nextButtonChapitre');
    const nextButtonFinish = document.getElementById('nextButtonFinish');

    // Enable the next button
    if (nextButton) {
        nextButton.disabled = false;
    }
    if (nextButtonChapitre) {
        nextButtonChapitre.disabled = false;
    }
    if (nextButtonFinish) {
        nextButtonFinish.disabled = false;
    }

    // Hide loading spinner if it exists
    const loadingSpinner = document.getElementById('loadingSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (loadingSpinner) {
        loadingSpinner.classList.add('d-none');
    }
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }

    // Update button text if it exists
    const buttonText = document.getElementById('buttonText');
    if (buttonText) {
        buttonText.textContent = 'Next';
    }
}

// Function to save or update course data
async function saveCourseData(formData) {
    fetch('/admin/save-course', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                courseId = data.courseId;
                document.getElementById('course_id').value = courseId;
           
            } else {
                alert('Error saving course data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the course data.');
        })
        .finally(() => {
            enableFormInputs();
        });
}

// Function to fetch course data
async function fetchCourseData(courseId) {
    fetch(`/admin/get-course/${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('course_id').value = data.course.id;
                document.getElementById('title').value = data.course.title;
                const urlCourLink = document.getElementById('url_cour');
                if (urlCourLink) {
                    const courseUrl = `https://maxskills.tn/formation/cour/${data.course.id}`;
                    urlCourLink.href = courseUrl;
                    urlCourLink.textContent = `Cours Url : ${courseUrl}`;
                }
                document.getElementById('keyword').value = data.course.keyword;
                document.getElementById('top_bar').value = data.course.top_bar;
                document.getElementById('button').value = data.course.button;
                document.getElementById('price_init').value = data.course.price_init;
                document.getElementById('price_promo').value = data.course.price_promo;
                document.getElementById('description').value = data.course.description;

                const visibilitySelect = document.getElementById('visibility');
                visibilitySelect.value = data.course.visibility;
    
                // If using Select2, refresh the dropdown
                if ($('.select2-icon').length) {
                    $('.select2-icon').select2({
                        width: "100%",
                        minimumResultsForSearch: Infinity, 
                        dropdownCssClass: "form-control", 
                        containerCssClass: "form-control", 
                        templateSelection: formatText,
                        templateResult: formatText
                    }).trigger('change');
                }

                if (data.course.path_banner) {
                    pond.addFile(data.course.path_banner);
                }
            } else {
                alert('Error fetching course data.');
            }
        })
        .catch(error => console.error('Error:', error));
}

async function fetchChapitreData(courseId) {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (loadingSpinner) {
        loadingSpinner.classList.remove('d-none');
    }
    if (loadingOverlay) {
        loadingOverlay.style.display = 'block';
    }

    fetch(`/admin/get-chapters/${courseId}`)
        .then(response => response.json())
        .then(data => {
            const handleList = document.getElementById('handleList');
            handleList.innerHTML = ''; // Vider la liste avant de la remplir

            if (data && data.length > 0) {
                data.forEach(chapter => {
                    const newLi = document.createElement('li');
                    newLi.className = 'row accordion app-accordion accordion-light-secondary';
                    newLi.setAttribute('data-chapter-id', chapter.id);

                    // Déterminer si le chapitre est actif (status = 1)
                    const isChecked = chapter.status == 1 ? 'checked' : '';

                    newLi.innerHTML = `
                        <div class="accordion-item">
                            <div class="accordion-header d-flex align-items-center w-100">
                                <!-- Drag Handle -->
                                <i class="ti ti-grip-vertical list-handle me-2" style="cursor: grab;"></i>

                                <!-- Resume Image -->
                                <img src="${chapter.path_resume || 'https://via.placeholder.com/80x45'}" alt="Résumé" class="img-thumbnail me-3" style="width: 80px; height: 45px; object-fit: cover;">

                                <!-- Accordion Button (takes remaining space ) -->
                                <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-${chapter.id}" aria-expanded="false"
                                        aria-controls="flush-collapse-${chapter.id}">
                                    <span class="chapter-title" onclick="event.stopPropagation(); getChapitreData('${chapter.id}')">${chapter.title}</span>
                                </button>

                                <!-- Toggle Switch -->
                                <div class="d-flex align-items-center ms-3 me-3">
                                    <label class="toggle-switch">
                                        <input type="checkbox" onchange="toggleChapterStatus(${chapter.id})" ${isChecked}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div id="flush-collapse-${chapter.id}" class="accordion-collapse collapse" data-bs-parent="#handleList">
                                <div class="accordion-body">
                                    <ul style="color:#000; margin-left:25px;" id="list-lessons-${chapter.id}">
                                        <!-- Les leçons seront chargées ici -->
                                    </ul>
                                    <div class="row">
                                        <div class="col-auto btn1">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="openAddLessonModal('${chapter.id}','','typelesson')">
                                                <i class="ti ti-file-plus"></i> Leçon
                                            </button>
                                        </div>
                                        <div class="col-auto btn2">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="openAddLessonModal('${chapter.id}','','typeprojet')">
                                                <i class="ti ti-file-zip"></i> Projet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    const lessonsList = newLi.querySelector(`#list-lessons-${chapter.id}`);
                    chapter.lessons.forEach(lesson => {
                        const lessonLi = document.createElement('li');
                        lessonLi.style.marginBottom = '15px';
                        lessonLi.setAttribute('data-lesson-id', lesson.id);
                        lessonLi.setAttribute('role', 'button');
                        const iconClass = lesson.type === 'projet' ? 'ti ti-file-zip' : 'ti ti-file-plus';
                        lessonLi.innerHTML = `<i class="${iconClass}"></i> ${lesson.title}`;
                        lessonLi.addEventListener('click', () => handleLessonClick(lesson, lesson.type));
                        lessonsList.appendChild(lessonLi);
                    });

                    handleList.appendChild(newLi);
                });
            } else {
                console.log('No chapters found.');
            }
        })
        .catch(error => console.error('Error fetching chapters:', error))
        .finally(() => {
            if (loadingSpinner) loadingSpinner.classList.add('d-none');
            if (loadingOverlay) loadingOverlay.style.display = 'none';
        });
}

// Nouvelle fonction pour gérer le changement de statut du chapitre
function toggleChapterStatus(chapterId) {
    fetch(`/admin/chapitres/${chapterId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`Statut du chapitre ${chapterId} mis à jour : ${data.status}`);
            // Optionnel : Afficher une notification de succès
            Toastify({
                text: "Statut du chapitre mis à jour avec succès.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
            }).showToast();
        } else {
            console.error('Erreur lors de la mise à jour du statut.');
            // Revenir à l'état précédent en cas d'erreur
            const checkbox = document.querySelector(`li[data-chapter-id="${chapterId}"] input[type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
            }
        }
    })
    .catch(error => {
        console.error('Erreur réseau:', error);
        const checkbox = document.querySelector(`li[data-chapter-id="${chapterId}"] input[type="checkbox"]`);
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
        }
    });
}

async function fetchCertificaData(courseId) {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (loadingSpinner) {
        loadingSpinner.classList.remove('d-none');
    }
    if (loadingOverlay) {
        loadingOverlay.style.display = 'block';
    }

    // Update button text if it exists
    const buttonText = document.getElementById('buttonText');
    if (buttonText) {
        buttonText.textContent = 'Loading...';
    }

    fetch(`/admin/get-certifica-test/${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                // Clear the existing content of the list (optional)
                const handleList = document.getElementById('certificaList');
                handleList.innerHTML = '';

                // Loop through each chapter
                data.forEach(chapter => {
                    // Create the <li> element for the chapter
                    const newLi = document.createElement('li');
                    newLi.className = 'row accordion app-accordion accordion-light-secondary';
                    newLi.setAttribute('data-certifica-id', chapter.id);
                    newLi.innerHTML = `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-${chapter.id}" aria-expanded="false"
                                        aria-controls="flush-collapse-${chapter.id}">
                                    <i class="ti ti-grip-vertical list-handle"></i>
                                     <span class="chapter-title" onclick="getCertificaData('${chapter.id}','${chapter.title}','${chapter.description}','${chapter.path_banner}','${chapter.timer_hours}','${chapter.timer_minutes}','${chapter.timer_seconds}')">${chapter.title}</span>
                                </button>
                            </h2>
                            <div id="flush-collapse-${chapter.id}" class="accordion-collapse collapse"
                                 data-bs-parent="#accordion1">
                                <div class="accordion-body">
                                    <ul style="color:#000;margin-left:25px;" id="list-test-${chapter.id}">
                                        
                                    </ul>
                                    <div class="row">
                                        <div class="col-auto btn2">
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="openAddLessonModal('${chapter.id}','','typetest')">
                                                <i class="ti ti-file-pencil"></i>
                                                Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Find the <ul> inside the accordion body to append lessons
                    const lessonsList = newLi.querySelector(`#list-test-${chapter.id}`);

                    // Loop through each lesson in the chapter
                    chapter.lessons.forEach(lesson => {
                        const lessonLi = document.createElement('li');
                        lessonLi.style.marginBottom = '15px';
                        lessonLi.setAttribute('data-test-id', lesson.id);
                        lessonLi.setAttribute('role', 'button');
                        lessonLi.setAttribute('tabindex', '0');
                        const icon = document.createElement('i');
                        icon.className = 'ti ti-tic-tac';
                        const titleText = document.createTextNode(` ${lesson.title}`);
                        lessonLi.appendChild(icon);
                        lessonLi.appendChild(titleText);
                        lessonLi.addEventListener('click', () => handleLessonClick(lesson, lesson.type));
                        lessonsList.appendChild(lessonLi);
                    });

                    // Append the chapter <li> to the parent <ul> with ID 'handleList'
                    handleList.appendChild(newLi);
                });
            } else {
                console.log('No Test & Certifica found.');
                const loadingSpinner = document.getElementById('loadingSpinner');
                const loadingOverlay = document.getElementById('loadingOverlay');

                if (loadingSpinner) {
                    loadingSpinner.classList.add('d-none');
                }
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }

                // Update button text if it exists
                const buttonText = document.getElementById('buttonText');
                if (buttonText) {
                    buttonText.textContent = 'Next';
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loadingOverlay = document.getElementById('loadingOverlay');

            if (loadingSpinner) {
                loadingSpinner.classList.add('d-none');
            }
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        });
}
function createLinkRow(title = '', url = '') {
    const linksContainer = document.getElementById('lesson-links-container');
    if (!linksContainer) return;

    const linkRow = document.createElement('div');
    linkRow.className = 'link-row';

    linkRow.innerHTML = `
        <input type="text" name="link_titles[]" class="form-control" placeholder="Titre du lien" value="${title}" required>
        <input type="url" name="link_urls[]" class="form-control" placeholder="https://example.com" value="${url}" required>
        <button type="button" class="btn btn-sm btn-danger remove-link-btn">
            <i class="ti ti-trash"></i>
        </button>
    `;

    linkRow.querySelector('.remove-link-btn' ).addEventListener('click', function () {
        linkRow.remove();
    });

    linksContainer.appendChild(linkRow);
}
function handleLessonClick(lesson, typeClicked) {
    // For creating a new lesson/project
    if (!lesson.id) {
        // The 'lesson' object here is just a placeholder from the loop,
        // so we pass `null` to indicate we're creating a new item.
        openAddLessonModal(lesson.chapitre_id, null, typeClicked);
        return;
    }

    // For editing an existing lesson/project
    fetch(`/admin/get-lesson-details/${lesson.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openAddLessonModal(data.lesson.chapitre_id, data.lesson, typeClicked);
            } else {
                alert('Error fetching lesson details.');
            }
        })
        .catch(error => console.error('Error:', error));
}
function openAddLessonModal(chapterId, lesson, typeModal) {
    // --- 1. Reset the form and clear old data ---
    const lessonForm = document.getElementById('lessonForm');
    lessonForm.reset();
    document.getElementById('lesson-links-container').innerHTML = '';

    // ** THE FIX IS HERE **
    // We now safely check if the FilePond instances exist on the window object before calling removeFiles().
    if (window.lessonImage && typeof window.lessonImage.removeFiles === 'function') {
        window.lessonImage.removeFiles();
    }
    if (window.lessonVideo && typeof window.lessonVideo.removeFiles === 'function') {
        window.lessonVideo.removeFiles();
    }
    if (window.lessonSourceFiles && typeof window.lessonSourceFiles.removeFiles === 'function') {
        window.lessonSourceFiles.removeFiles();
    }

    // --- 2. Get DOM element references ---
    document.getElementById('chapterIdForLesson').value = chapterId;
    document.getElementById('idLesson').value = lesson ? lesson.id : '';
    const modalTitle = document.getElementById('addLessonModalLabel');
    const typeModalInput = document.getElementById('typeModal');
    const linksSection = document.getElementById('lesson-links-section');

    // --- 3. Determine visibility based on type ---
    const isLesson = typeModal === 'typelesson' || (lesson && lesson.type === 'lesson');

    if (isLesson) {
        modalTitle.textContent = lesson ? 'Modifier la Leçon' : 'Ajouter une Leçon';
        typeModalInput.value = 'lesson';
        linksSection.style.display = 'block';
    } else {
        modalTitle.textContent = lesson ? 'Modifier le Projet' : 'Ajouter un Projet';
        typeModalInput.value = 'projet';
        linksSection.style.display = 'none';
    }

    // --- 4. Populate the form if we are editing ---
    if (lesson) {
        document.getElementById('lessonTitle').value = lesson.title;
        document.getElementById('lessonDescription').value = lesson.description;
        document.getElementById('lessonVideoHours').value = lesson.lessonVideoHours;
        document.getElementById('lessonVideoMinutes').value = lesson.lessonVideoMinutes;
        document.getElementById('lessonVideoSeconds').value = lesson.lessonVideoSeconds;
        document.getElementById('lessonOrderNum').value = lesson.order_num;

        if (lesson.path_icon && window.lessonImage) window.lessonImage.addFile(lesson.path_icon);
        // Note: You might need to adjust the logic for video/source files if they are not just simple files.
        // For now, this assumes you want to show the file name.
        if (lesson.path_video && window.lessonVideo) window.lessonVideo.addFile(lesson.path_video);
        if (lesson.path_projet && window.lessonSourceFiles) window.lessonSourceFiles.addFile(lesson.path_projet);

        // Populate existing URL links
        if (lesson.urls && lesson.urls.length > 0) {
            lesson.urls.forEach(urlItem => {
                createLinkRow(urlItem.title, urlItem.url);
            });
        }
    }

    // --- 5. Show the modal ---
    const addLessonModal = new bootstrap.Modal(document.getElementById('addLessonModal'));
    addLessonModal.show();
}

function publishLessons(event) {

    disableFormModalLessonInputs();

    try {
        const formDataLesson = new FormData();

        // Append form data
        formDataLesson.append('idLesson', document.getElementById('idLesson').value);
        formDataLesson.append('chapitre_id', document.getElementById('chapterIdForLesson').value);
        formDataLesson.append('title', document.getElementById('lessonTitle').value);
        formDataLesson.append('description', document.getElementById('lessonDescription').value);
        formDataLesson.append('lessonVideoHours', document.getElementById('lessonVideoHours').value);
        formDataLesson.append('lessonVideoMinutes', document.getElementById('lessonVideoMinutes').value);
        formDataLesson.append('lessonVideoSeconds', document.getElementById('lessonVideoSeconds').value);
        formDataLesson.append('typeModal', document.getElementById('typeModal').value);
        formDataLesson.append('new_order', document.getElementById('lessonOrderNum').value);
        
        const linkTitles = document.querySelectorAll('input[name="link_titles[]"]');
        const linkUrls = document.querySelectorAll('input[name="link_urls[]"]');

        // Loop through all the title inputs and append them to formDataLesson
        linkTitles.forEach(input => {
            formDataLesson.append('link_titles[]', input.value);
        });

        // Loop through all the URL inputs and append them to formDataLesson
        linkUrls.forEach(input => {
            formDataLesson.append('link_urls[]', input.value);
        });

        // Append files
        const lessonImg = window.lessonImage.getFiles();
        if (lessonImg.length > 0) {
            formDataLesson.append('path_img', lessonImg[0].file);
        }

        const LessonVid = window.lessonVideo.getFiles();
        if (LessonVid.length > 0) {
            formDataLesson.append('path_video', LessonVid[0].file);
        }

        const lessonFiles = window.lessonSourceFiles.getFiles();
        if (lessonFiles.length > 0) {
            formDataLesson.append('path_source', lessonFiles[0].file);
        } else {
            formDataLesson.append('delete_source_file', '1');
        }

        // Show the progress bar
        const progressBar = document.querySelector('.progress-bar');
        const progressContainer = document.getElementById('progress-bar-loading');
        progressContainer.classList.remove('d-none');
        progressBar.style.width = '0%'; // Reset the progress bar
        progressBar.textContent = '0%';
        
        // Use XMLHttpRequest for progress tracking
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/ajout-Lecon', true);

        xhr.upload.onprogress = (event) => {
            if (event.lengthComputable) {
                disableFormModalLessonInputs();
                const progressContainer = document.getElementById('progress-bar-loading');
                progressContainer.classList.remove('d-none');

                const percentCompleted = Math.round((event.loaded * 100) / event.total);
                progressBar.style.width = percentCompleted + '%'; // Update the width
                progressBar.textContent = percentCompleted + '%'; // Update the text
                progressBar.setAttribute('aria-valuenow', percentCompleted); // Update ARIA value
       
            }
        };

        xhr.onload = () => {
            if (xhr.status === 200) {
                const result = JSON.parse(xhr.responseText);
                const progressBar = document.querySelector('.progress-bar');
                const progressContainer = document.getElementById('progress-bar-loading');
                progressContainer.classList.add('d-none');
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
                enableFormModalLessonInputs();
                if (result.success) {
                    const lessonFormInput = document.getElementById('lessonForm');
                    lessonFormInput.reset();
                    handleToastModalSucceLesson();
                    if (window.lessonImage) {
                        window.lessonImage.removeFiles();
                    }
                    if (window.lessonVideo) {
                        window.lessonVideo.removeFiles();
                    }
                    if (window.lessonSourceFiles) {
                        window.lessonSourceFiles.removeFiles();
                    }
                    bootstrap.Modal.getInstance(document.getElementById('addLessonModal')).hide();
                    
                    if (result.lesson.type == 'test_final') {
                        showStep(3);
                    } else {
                        showStep(2);
                    }
                } else {
                    alert('Erreur lors de l\'ajout de la leçon: ' + result.message);
                }
            } else {
                const progressBar = document.querySelector('.progress-bar');
                const progressContainer = document.getElementById('progress-bar-loading');
                progressContainer.classList.add('d-none');
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
                enableFormModalLessonInputs();
                alert('Erreur lors de l\'envoi des données.');
            }
        };

        xhr.onerror = () => {
            alert('Une erreur s\'est produite lors de l\'envoi des données.');
        };

        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.send(formDataLesson);

    } catch (error) {
        console.error('Error:', error);
        alert('Une erreur s\'est produite lors de l\'envoi des données.');
    } finally {
        const progressBar = document.querySelector('.progress-bar');
        const progressContainer = document.getElementById('progress-bar-loading');
        progressContainer.classList.add('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
   
        enableFormModalLessonInputs();
    }
}

//Chapitre modal update
function getChapitreData(id){

    const addChapterModal = new bootstrap.Modal(document.getElementById('addChapterModal'));
        fetch(`/admin/details_chapitre/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('id_chapitre').value = data.chapitre.id;
                document.getElementById('chapterTitle').value = data.chapitre.title;
                document.getElementById('chapterDescription').value = data.chapitre.description;
                if(data.chapitre.path_banner)
                {
                    window.pondBanner.addFile(data.chapitre.path_banner);
                }
                if(data.chapitre.path_resume)
                {
                    const pathResumeLabel = document.querySelector('label[for="path_resume"]');
                    if (pathResumeLabel) {
                        pathResumeLabel.addEventListener('click', function(event) {
                            event.preventDefault();
                            window.open(data.chapitre.path_resume, '_blank');
                        });
                        pathResumeLabel.style.cursor = 'pointer';
                        pathResumeLabel.style.color = 'rgba(248, 153, 68, 1)';
                        pathResumeLabel.style.textDecoration = 'underline';
                    }
                }

                document.getElementById('deleteChapitreBtn').removeAttribute('onclick');
                document.getElementById('timer_hours_chapitre').value = data.chapitre.timer_hours;
                document.getElementById('timer_minutes_chapitre').value = data.chapitre.timer_minutes;
                document.getElementById('timer_seconds_chapitre').value = data.chapitre.timer_seconds;
                document.getElementById('publishChapterBtn').textContent = 'Modifier';
                document.getElementById('deleteChapitreBtn').textContent = 'Supprimer';
                document.getElementById('deleteChapitreBtn').classList.remove('btn-secondary');
                document.getElementById('deleteChapitreBtn').classList.add('btn-danger');
                document.getElementById('deleteChapitreBtn').setAttribute('onclick', `deleteChapitre(${data.chapitre.id},'${data.chapitre.title}')`);
                document.getElementById('deleteChapitreBtn').removeAttribute('data-bs-dismiss');
            
            } else {
                alert('Une erreur s\'est produite lors de la récupération du chapitre.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur s\'est produite lors de la récupération du chapitre.');
        })
        .finally(() => {
            addChapterModal.show();
        });
}

// Get the modal element
const addChapterModalEl = document.getElementById('addChapterModal');

// Listen for when the modal is about to be shown
addChapterModalEl.addEventListener('show.bs.modal', event => {
    // Check if the FilePond instance for the resume image already exists.
    // If it doesn't, create it.
    if (!window.pondResume) {
        const resumeInput = document.querySelector('#path_resume');
        
        // Make sure the input element exists before creating FilePond
        if (resumeInput) {
            window.pondResume = FilePond.create(resumeInput, {
                allowMultiple: false,
                acceptedFileTypes: ['image/*'],
                maxFileSize: '250MB',
                labelIdle: 'Glissez-déposez votre photo ou <span class="filepond--label-action">Parcourir</span>',
                allowFileTypeValidation: true,
                allowFileSizeValidation: true,
            });
        }
    }
});

function getCertificaData(id,title,description,path_banner,timer_hours,timer_minutes,timer_seconds){

    const addCertificaModal = new bootstrap.Modal(document.getElementById('addCertificaModal'));

    document.getElementById('cour_id').value = document.getElementById('course_id').value;
    document.getElementById('id_chapitre').value = id;
    document.getElementById('certificaTitle').value = title;
    document.getElementById('certificaDescription').value = description;
    
    if(path_banner)
    {
        window.pondBannerCertifica.addFile(path_banner);
    }

    document.getElementById('deleteCertificaBtn').removeAttribute('onclick');
    document.getElementById('timer_hours_certifica').value = timer_hours;
    document.getElementById('timer_minutes_certifica').value = timer_minutes;
    document.getElementById('timer_seconds_certifica').value = timer_seconds;
    document.getElementById('publishCertificaBtn').textContent = 'Modifier';
    document.getElementById('deleteCertificaBtn').textContent = 'Supprimer';
    document.getElementById('deleteCertificaBtn').classList.remove('btn-secondary');
    document.getElementById('deleteCertificaBtn').classList.add('btn-danger');
    document.getElementById('deleteCertificaBtn').setAttribute('onclick', `deleteChapitre(${id},'${title}')`);
    document.getElementById('deleteCertificaBtn').removeAttribute('data-bs-dismiss');
    addCertificaModal.show();
}

//Lesson Toast
const handleToastModalSucceLesson = () => {
    $("#step-2 .app-color-toast.app-toast-success.lesson-toast").removeClass('d-none');
    setTimeout(function () {
        $("#step-2 .app-color-toast.app-toast-success.lesson-toast").addClass("d-none");
    }, 5000);
}

// Function to disable all form inputs
function disableFormModalLessonInputs() {

    const inputs = document.querySelectorAll('#addLessonModal input,  #addLessonModal textarea, #addLessonModal button');
    inputs.forEach(input => {
        input.disabled = true;
    });
    const publishLessonBtn = document.getElementById('publishLessonBtn');
    const loadingSpinnerLessonModal = document.getElementById('loadingSpinnerLessonModal');

    publishLessonBtn.disabled = true;
    loadingSpinnerLessonModal.classList.remove('d-none');

    // Update button text if it exists
    const publishLessonBtnText = document.getElementById('publishLessonBtnText');
    if (publishLessonBtnText) {
        publishLessonBtnText.textContent = 'Loading...';
    }
}

// Function to enable all form inputs
function enableFormModalLessonInputs() {
    const inputs = document.querySelectorAll('#addLessonModal input,  #addLessonModal textarea, #addLessonModal button');
    inputs.forEach(input => {
        input.disabled = false;
    });

    const publishLessonBtns = document.getElementById('publishLessonBtn');
    const loadingSpinnerLessonModal = document.getElementById('loadingSpinnerLessonModal');

    publishLessonBtns.disabled = false;
    loadingSpinnerLessonModal.classList.add('d-none');
    // Update button text if it exists
    const publishLessonBtn = document.getElementById('publishLessonBtnText');
    if (publishLessonBtn) {
        publishLessonBtn.textContent = 'Publier';
    }
}

//btn funtion
async function nextButtonFunction(){
    if (!validateStep(1)) {
        alert('Please fill out all required fields before proceeding.');
        return;
    }
    disableFormInputs();
    // Prepare form data
    const formData = new FormData();
    formData.append('course_id', document.getElementById('course_id').value);
    formData.append('title', document.getElementById('title').value);
    formData.append('keyword', document.getElementById('keyword').value);
    formData.append('top_bar', document.getElementById('top_bar').value);
    formData.append('button', document.getElementById('button').value);
    formData.append('price_init', document.getElementById('price_init').value);
    formData.append('price_promo', document.getElementById('price_promo').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('visibility', document.getElementById('visibility').value);

    pond.getFiles().forEach(file => {
        formData.append('file', file.file);
    });

    // Only call saveCourseData if we're moving from step 1 to step 2
    await saveCourseData(formData);
    showStep(2);
    
   
}
function nextButtonChapitreFunction(){
    showStep(3);
}
function nextButtonCertificaFunction(){
    showStep(4);
}
function nextButtonFinishFunction(){
    window.location.href = '/admin/cours';
}