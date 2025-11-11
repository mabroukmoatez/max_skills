<div class="modal fade" id="addChapterModal" tabindex="-1" aria-labelledby="addChapterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addChapterModalLabel">Chapitre</h5>
                
                <!-- Buttons in the Header -->
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteChapitreBtn">Annuler</button>
                    <button type="button" class="btn btn-primary" id="publishChapterBtn">Publier</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div id="loadingSpinnerChapitreModal" class="d-none text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Création du chapitre en cours...</p>
                </div>
                <form id="chapterForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="cour_id" id="cour_id" value="{{ $course->id ?? '' }}">
                    <input type="hidden" name="id_chapitre" id="id_chapitre" value="">
                    <div class="row">
                        <!-- Left Part (col-lg-9) -->
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label for="chapterTitle" class="form-label">Titre du chapitre</label>
                                <input type="text" class="form-control" id="chapterTitle" name="chapterTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="chapterDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="chapterDescription" name="chapterDescription" rows="3" required></textarea>
                            </div>
                        </div>
 
                        <!-- Right Part (col-lg-3) -->
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label for="path_banner" class="form-label wizard-form-text-label">Image du chapitre</label>
                                
                                <input class="filepond" type="file" id="path_banner" name="path_banner" data-allow-reorder="true" required>
                               
                            </div>
                            <div class="mb-4">
                                <label for="path_resume" class="form-label wizard-form-text-label">Photo résumé du chapitre</label>
                                <input class="filepond" type="file" id="path_resume" name="path_resume" data-allow-reorder="true" required>
                            </div>
                            <div class="mb-4">
                                <label for="projectVideoTime" class="form-label">Temp de vidéo</label>
                                <div class="row">
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_hours_chapitre" name="timer_hours_chapitre" placeholder="Heures" min="0" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_minutes_chapitre" name="timer_minutes_chapitre" placeholder="Minutes" min="0" max="59" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_seconds_chapitre" name="timer_seconds_chapitre" placeholder="Secondes" min="0" max="59" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDeleteChapitreConfirm" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suppression du chapitre</h5>
                <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 text-center align-self-center">
                        <img src="../assets/images/modals/04.png" alt="" class="img-fluid b-r-10">
                    </div>
                    <div class="col-lg-9 ps-4">
                        <h5 id="chapterTitleInModal"></h5>
                        <ul class="mt-3 mb-0 list-disc">
                            <li>Si vous confirmez, le chapitre sera supprimé avec ses leçons définitivement du serveur.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary">Confirmer</button>
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>