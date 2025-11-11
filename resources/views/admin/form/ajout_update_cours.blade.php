<form id="wizardForm" method="POST" class="form col-lg-12 row" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="course_id" id="course_id" value="{{ $course->id ?? '' }}">
    <div class="left-section  col-lg-9">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="title" class="form-label wizard-form-text-label">Titre de cour</label>
                    <input type="text" minlength="20" class="form-control wizard-required" placeholder="Titre de cour" name="title" id="title">
                    <a id="url_cour" href="#" target="_blank" style="color: #000;"></a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="keyword" class="form-label wizard-form-text-label">Mot clé</label>
                    <input type="text" class="form-control wizard-required" id="keyword" name="keyword" placeholder="Mot clé">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="top_bar" class="form-label wizard-form-text-label">Haut de page</label>
                    <input type="text" class="form-control wizard-required" id="top_bar" name="top_bar" placeholder="Haut de page">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="button" class="form-label wizard-form-text-label">Bouton</label>
                    <input type="text" class="form-control wizard-required" id="button" name="button" placeholder="Commencer">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="price_init" class="form-label wizard-form-text-label">Prix Standard</label>
                    <input type="text" class="form-control wizard-required" id="price_init" name="price_init" placeholder="120.000">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="price_promo" class="form-label wizard-form-text-label">Prix en Promotion</label>
                    <input type="text" class="form-control" id="price_promo" name="price_promo" placeholder="0.000">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="description" class="form-label wizard-form-text-label">Description</label>
                    <textarea class="form-control wizard-required" id="description" name="description" placeholder="Description" rows="8" required></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="right-section col-lg-3 pe-0">
        <div class="col-lg-12">
            <div class="mb-3">
                <label for="visibility" class="form-label wizard-form-text-label">Visibilité</label>
                <select class="form-control wizard-required select2-icon" id="visibility" name="visibility">
                    <option value="1" data-icon="ph ph-eye">Publique
                    </option>
                    <option value="0" data-icon="ph ph-eye-slash">Privé
                    </option>
                    <option value="2" data-icon="ph ph-eye-slash">Archivée
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="file" class="form-label wizard-form-text-label">Image de cour / Vidéo Intro</label>
                <div class="file-uploader-box">
                    <input type="file" class="filepond" id="file" name="file" accept="image/*, video/*">
                </div>
            </div>
        </div>
    </div>
</form>