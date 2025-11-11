<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white"
            style="background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgba(0, 18, 29, 0.36) 100%); border: 2px solid rgb(248, 152, 79); border-radius: 30px; padding: 30px;">
            <div class="modal-body" style="box-shadow:none !important;">
                <h2 class="text-center fw-medium mb-3" style="font-size: 23px !important">Profile Etudiant</h2>
                <p class="text-center mb-4" style="font-size: 12px !important">Modifier votre information de profile
                    dans cette page et changer votre mot de passe.<br><small class="text-white">Tous ces informations
                        sont confidentiel et sécurisé!!</small></p>

                <!-- Profile Image -->
                <!-- Add this inside your form with id="update-user-form" -->
                <div class="profile-row mb-4 text-center">
                    <div class="profile-pic-container">
                        <img src="{{ auth()->user()->path_photo ? 'https://maxskills.tn/' . auth()->user()->path_photo : asset('assets/images/ai_avtar/2.jpg') }}"
                            alt="Photo de profil" class="profile-pic" id="profile-pic-preview">
                        <input type="file" name="path_photo" id="path_photo" style="display: none;">
                    </div>
                    <div class="profile-details ms-3">
                        @error('path_photo')
                            <span class="text-danger" id="path_photo-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <!-- Profile Form -->
                <form method="POST" action="" id="update-user-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control bg-dark text-white border-0" name="firstname"
                                value="{{ auth()->user()->firstname }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control bg-dark text-white border-0" name="name"
                                value="{{ auth()->user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse Postal</label>
                            <input type="text" class="form-control bg-dark text-white border-0" name="location"
                                value="{{ auth()->user()->location }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro de téléphone</label>
                            <input type="text" class="form-control bg-dark text-white border-0" name="phone"
                                value="{{ auth()->user()->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse E-mail</label>
                            <input type="email" class="form-control bg-dark text-white border-0" name="email"
                                value="{{ auth()->user()->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control bg-dark text-white border-0" name="password">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center  gap-3 mt-4">
                        <button type="button" class="btn"
                            style="border-radius:30px;background-color: #646464; color: white;"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn"
                            style="border-radius:30px;background-color: #F8994F; color: black;">Mise à jours</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Phone Number Warning Modal -->
<div class="modal fade" id="phoneWarningModal" tabindex="-1" aria-labelledby="phoneWarningModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content text-white" style="background: linear-gradient(129.82deg, rgba(247, 133, 44, 0.9) 0%, rgba(0, 18, 29, 0.36) 100%); border: 2px solid rgb(248, 152, 79); border-radius: 30px; padding: 30px;">
      <div class="modal-body" style="box-shadow:none !important;">
        <h2 class="text-center fw-medium mb-3" style="font-size: 23px !important">Action Requise</h2>
        <p class="text-center mb-4" style="font-size: 12px !important">Pour accéder à toutes les fonctionnalités de MaxSkills, veuillez ajouter un numéro de téléphone valide.</p>

        <!-- Phone Input Form -->
        <form id="phone-warning-form">
            @csrf
            <div class="mb-3">
              <label for="phone_tunisian" class="form-label">Numéro de téléphone Tunisien</label>
              <div class="phone-input-group">
                <span class="phone-prefix">+216</span>
                <input type="tel" class="form-control" id="phone_tunisian" name="phone" placeholder="Numéro de téléphone" required>
              </div>
              <div id="phone-error-message" class="text-danger mt-2" style="font-size: 12px; display: none;"></div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" class="btn" style="border-radius:30px; background-color: #F8994F; color: black;">Enregistrer et Continuer</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


<footer class="footer">
    <!-- Première rangée -->
    <div class="footer-row">
        <div class="footer-logo">
            <img src="{{ asset('client/images/logo.png') }}" alt="Logo">
        </div>
        <div class="footer-links">
            <ul>
                <li><a href="#">Support</a></li>
                <li>|</li>
                <li><a href="#">Politique de confidentialité</a></li>
                <li>|</li>
                <li><a href="#">Conditions</a></li>
            </ul>
        </div>
    </div>
    <hr style="margin:0 !important;">
    <!-- Deuxième rangée -->
    <div class="footer-row-second">
        <div class="footer-subscribe">
            <input type="email" placeholder="email">
            <button>S'abonner</button>
        </div>
        <div class="footer-copyright">
            <p>Made by El Mabrouk Developpement | Copyright 2025 Maxskills</p>
        </div>
    </div>
</footer>
