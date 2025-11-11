<!-- Modal for adding a new lesson -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addLessonModalLabel">Ajouter une Leçon</h5>
                <!-- Buttons in the Header -->
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger me-2" id="deleteLessonProjet" onclick="triggerDeleteConfirmation()">Supprimer</button>

                    <button type="button" class="btn btn-primary" id="publishLessonBtn" onclick="publishLessons(event)">
                        <span id="publishLessonBtnText">Publier</span>
                    </button>
                </div>
            </div> 

            <!-- Modal Body -->
            <div class="modal-body">
                <div id="loadingSpinnerLessonModal" class="d-none text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p id="loadingModalLessonText">Création de la leçon en cours...</p>
                </div>
                
                <form id="lessonForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="chapter_id" id="chapterIdForLesson">
                    <input type="hidden" name="idLesson" id="idLesson" value="">
                    <input type="hidden" name="typeModal" id="typeModal" value="">

                    <div class="row">
                        <!-- Left Part (col-lg-9) -->
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label for="lessonTitle" class="form-label">Titre de la leçon</label>
                                <input type="text" class="form-control" id="lessonTitle" name="lessonTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="lessonDescription" class="form-label">Description de la leçon</label>
                                <textarea class="form-control" id="lessonDescription" name="lessonDescription" rows="12" required></textarea>
                            </div>

                            <div id="lesson-links-section" class="mb-3" style="display: none;">
                                <label class="form-label">Liens de la Leçon</label>
                                <div id="lesson-links-container">
                                    <!-- Dynamic link rows will be inserted here by JavaScript -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-lesson-link-btn">
                                    <i class="ti ti-plus"></i> Ajouter un lien
                                </button>
                            </div>
                        </div>

                        <!-- Right Part (col-lg-3) -->
                        <div class="col-lg-4">
                           
                            <div class="mb-4 file-uploader-box">
                                <div class="col">
                                    <label for="projectImage" class="form-label wizard-form-text-label">Image du la leçon</label>
                                    <input class="filepond m-auto" type="file" id="lessonImage" name="lessonImage"
                                           accept="image/png, image/jpeg, image/gif" required>
                                </div>
                            </div>
                            
                            <div class="mb-4 file-uploader-box">
                                <div class="col">
                                    <label for="lessonVideo" class="form-label wizard-form-text-label">Vidéo de la leçon</label>
                                    <input class="filepond m-auto" type="file" id="lessonVideo" name="lessonVideo"
                                           accept="video/mp4, video/webm, video/ogg" required>
                                </div>
                            </div>
                            <div class="progress w-100 d-none" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                aria-valuemax="100"  id="progress-bar-loading" style="margin-bottom:10px;">
                                <div class="progress-bar bg-primary progress-bar-striped" style="width: 1%"> 0% </div>
                            </div>
                            <div class="mb-4 file-uploader-box">
                                <div class="col">
                                    <label for="lessonSourceFiles" class="form-label wizard-form-text-label">Fichiers sources de la leçon</label>
                                    <input class="filepond m-auto" type="file" id="lessonSourceFiles" name="lessonSourceFiles"
                                           accept="application/zip,zip,application/octet-stream,application/x-zip,application/x-zip-compressed">
                                </div>
                            </div>
                           
                            <div class="mb-4">
                                <label for="lessonVideoTime" class="form-label">Temps de la vidéo</label>
                                <div class="row">
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="lessonVideoHours" name="lessonVideoHours" placeholder="Heures" min="0" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="lessonVideoMinutes" name="lessonVideoMinutes" placeholder="Minutes" min="0" max="59" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="lessonVideoSeconds" name="lessonVideoSeconds" placeholder="Secondes" min="0" max="59" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="lessonOrderNum" class="form-label">Numéro d'ordre</label>
                                <input type="number" class="form-control" id="lessonOrderNum" name="order_num" placeholder="Ordre" min="1">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ModalDeleteLessonProjetConfirm" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suppression</h5>
                <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 text-center align-self-center">
                        <img src="{{ asset('assets/images/modals/04.png') }}" alt="" class="img-fluid b-r-10">
                    </div>
                    <div class="col-lg-9 ps-4">
                        <h5 id="lessonProjetTitleInModal"></h5>
                        <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                        <ul class="mt-3 mb-0 list-disc">
                            <li>Si vous confirmez, le fichier sera supprimé définitivement du serveur.</li>
                            <li>Cette action est irréversible.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Annuler</button>
                <!-- MODIFIED: Changed button text for clarity -->
                <button type="button" class="btn btn-danger" id="confirmDeleteLessonBtn">Confirmer la Suppression</button>
            </div>
        </div>
    </div>
</div>