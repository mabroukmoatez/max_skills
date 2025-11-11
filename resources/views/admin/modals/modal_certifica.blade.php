<div class="modal fade" id="addCertificaModal" tabindex="-1" aria-labelledby="addCertificaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addCertificaModalLabel">Certifica</h5>
                
                <!-- Buttons in the Header -->
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="deleteCertificaBtn">Annuler</button>
                    <button type="button" class="btn btn-primary" id="publishCertificaBtn">Publier</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div id="loadingSpinnerCertificaModal" class="d-none text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Création du Certifica en cours...</p>
                </div>
                <form id="CertificaForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="cour_id" id="cour_id" value="{{ $course->id ?? '' }}">
                    <input type="hidden" name="id_chapitre" id="id_chapitre" value="">
                    <div class="row">
                        <!-- Left Part (col-lg-9) -->
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label for="CertificaTitle" class="form-label">Titre du Certifica</label>
                                <input type="text" class="form-control" id="certificaTitle" name="certificaTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="CertificaDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="certificaDescription" name="certificaDescription" rows="3" required></textarea>
                            </div>
                        </div>
 
                        <!-- Right Part (col-lg-3) -->
                        <div class="col-lg-4">
                            <div class="mb-4">
                                <label for="path_banner_certifica" class="form-label wizard-form-text-label">Image du Certifica</label>
                                
                                <input class="filepond" type="file" id="path_banner_certifica" name="path_banner_certifica" data-allow-reorder="true" required>
                               
                            </div>
                           
                            <div class="mb-4" style="display:none;">
                                <label for="projectVideoTime" class="form-label">Temp estimé pour le test</label>
                                <div class="row">
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_hours_certifica" name="timer_hours_certifica" placeholder="Heures" value="0" min="0" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_minutes_certifica" name="timer_minutes_certifica" placeholder="Minutes" value="0" min="0" max="59" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control" id="timer_seconds_certifica" name="timer_seconds_certifica" placeholder="Secondes" value="0" min="0" max="59" required>
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

<div class="modal fade" id="ModalDeleteCertificaConfirm" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suppression du Certifica</h5>
                <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 text-center align-self-center">
                        <img src="../assets/images/modals/04.png" alt="" class="img-fluid b-r-10">
                    </div>
                    <div class="col-lg-9 ps-4">
                        <h5 id="CertificaTitleInModal"></h5>
                        <ul class="mt-3 mb-0 list-disc">
                            <li>Si vous confirmez, le Certifica sera supprimé avec ses leçons définitivement du serveur.</li>
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