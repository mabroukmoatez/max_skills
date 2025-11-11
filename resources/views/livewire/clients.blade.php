<div id="myTable">
    <!-- Search and Add Button -->
    <!-- Search, Filter, and Action Buttons -->
<div class="list-table-header mb-3">
    <!-- Top row: Search and main buttons -->
    <div class="d-flex justify-content-between align-items-center">
        <!-- Search Form -->
        <div class="col-lg-6">
            <form class="app-form app-icon-form w-100">
                <div class="position-relative">
                    <input type="search" class="form-control search" style="border-radius: 10px !important;" placeholder="Chercher un apprenant..." wire:model.live.debounce.300ms="search">
                    <i class="ti ti-search text-dark"></i>
                </div>
            </form>
        </div>
    
        <!-- Action Buttons -->
        <div class="col-lg-6 d-flex justify-content-end gap-2">
            <!-- Filter Button -->
            <button type="button" class="btn d-flex align-items-center" style="background-color: #e9ecef; color: #495057;" wire:click="toggleFilters">
                <i class="ti ti-filter fs-5 me-sm-1"></i>
                <p class="m-0" style="font-size:13px !important;">Filtrer</p>
            </button>

            <!-- Notification Button -->
            <button type="button" class="btn d-flex align-items-center" style="background-color:#F8994F !important;color:#fff !important;" wire:click="openSingleNotificationModal">
                <i class="ti ti-notification fs-5 me-sm-1"></i>
                <p class="m-0" style="font-size:13px !important;">Notification</p>
            </button>
        
            <!-- Add Client Button -->
            <button type="button" class="btn btn-primary d-flex align-items-center" wire:click="openModal(false)">
                <i class="ti ti-plus fs-5 me-sm-1"></i>
                <p class="m-0" style="font-size:13px !important;">Apprenant</p>
            </button>
        </div>
    </div>

    <!-- Filter Panel (conditionally displayed) -->
@if ($showFilters)
<div class="card mt-3" style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; animation: fadeIn 0.5s;">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <!-- Filtre par État Unifié -->
            <div class="col-md-6">
                <label for="filterState" class="form-label fw-bold">Filtrer par État</label>
                <select id="filterState" class="form-select" wire:model.live="filterState">
                    <option value="">Tous (sauf archivés) ({{ $nb_client }})</option>
                    <option value="actif">Payé ({{ $statusCounts['actif'] ?? 0 }})</option>
                    <option value="demo">En Démo ({{ $statusCounts['demo'] ?? 0 }})</option>
                    <option value="test">Test ({{ $statusCounts['test'] ?? 0 }})</option>
                    <option value="expire">Expiré ({{ $statusCounts['expire'] ?? 0 }})</option>
                    <option value="archive">Archivé ({{ $statusCounts['archive'] ?? 0 }})</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filterGoogle" class="form-label fw-bold">Connexion Google</label>
                <select id="filterGoogle" class="form-select" wire:model.live="filterGoogle">
                    <option value="">Tous les clients</option>
                    <option value="avec">Avec Google ({{ $statusCounts['avec_google'] ?? 0 }})</option>
                    <option value="sans">Sans Google ({{ $statusCounts['sans_google'] ?? 0 }})</option>
                </select>
            </div>

            <!-- Bouton Réinitialiser -->
            <div class="col-md-2 ">
                <label for="filterState" class="form-label fw-bold" style="
    color: #f8f9fa;
">Action</label>
                <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                    <i class="ti ti-reload me-1"></i>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>
</div>
@endif



</div>

<!-- Le reste de votre fichier (modals, table, etc.) reste inchangé -->


    <!-- Modal for Adding/Editing Clients -->
    @if ($isModalOpen)
        <div class="modal"  style="display: flex ; flex-direction: row; flex-wrap: wrap; align-content: space-around;">
            <div class="modal-dialog app_modal_lg">
                <div class="modal-content">
                    <div class="modal-body">
                        @if ($errorMessage)
                            <div class="alert alert-danger" role="alert">
                                {{ $errorMessage }}
                            </div>
                        @endif
                        <form wire:submit.prevent="saveClient">
                            <div wire:loading wire:target="openModal">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p>Chargement des données...</p>
                                </div>
                            </div>
                            <div wire:loading.remove wire:target="openModal">
                                <div class="mb-3 d-flex justify-content-center align-items-center">
                                    @if ($photo_client)
                                        <!-- Show the newly selected image -->
                                        <img src="{{ $photo_client->temporaryUrl() }}" alt="Preview" class="img-fluid col-lg-4" style="max-height: 150px;border-radius: 50%;max-width: 150px;">
                                    @elseif ($editMode && $existingPhoto)
                                        <!-- Show the existing image for editing -->
                                        <img src="{{ asset($existingPhoto) }}" alt="Preview" class="img-fluid col-lg-4" style="max-height: 150px;border-radius: 50%;max-width: 150px;">
                                    @else
                                        <!-- Show a placeholder if no image is selected -->
                                        <div class="text-muted">Aucune image sélectionnée</div>
                                    @endif
                                </div> 
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Photo:</label>
                                        <input type="file" class="form-control" id="photo_client" name="photo_client" wire:model="photo_client">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Prénom:</label>
                                        <input type="text" class="form-control" wire:model="firstname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nom de la famille:</label>
                                        <input type="text" class="form-control" wire:model="name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Adresse Postal:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <img src="{{ asset('img/icon_localisation.png') }}" alt="Lock Icon" style="width: 24px; height: 24px;">
                                            </span>
                                                <input type="text" class="form-control" wire:model="location" style="border-left:0px solid red;">
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Numéro de téléphone:</label>
                                         <div class="input-group">
                                            <span class="input-group-text">
                                                <img src="{{ asset('img/icon_phone.png') }}" alt="Lock Icon" style="width: 24px; height: 24px;">
                                            </span>
                                                  <input type="text" class="form-control" wire:model="phone" required  style="border-left:0px solid red;">
                                        </div>
                                  
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Adresse E-mail:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <img src="{{ asset('img/icon_email.png') }}" alt="Lock Icon" style="width: 24px; height: 24px;">
                                            </span>
                                             <input type="email" class="form-control" wire:model="email" required  style="border-left:0px solid red;">
                                        </div>
                                       
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                              
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label class="form-label">Mot de passe:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <img src="{{ asset('img/icon_lock.png') }}" alt="Lock Icon" style="width: 24px; height: 24px;">
                                            </span>
                                            <input type="password" class="form-control" wire:model="password"  style="border-left:0px solid red;">
                                        </div>
                                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label class="form-label">Moyen de Paiement:</label>
                                        <select class="form-select" wire:model="payment_getways" required>
                                            <option value="virement">Virement</option>
                                            <option value="d17">D17</option>
                                            <option value="flouci">Flouci</option>
                                            <option value="konnect">Konnect</option>
                                            <option value="espece">Espece</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2" style="display:none;">
                                  <div class="col-md-12">
                                        <label class="form-label">Formation:</label>
                                        <input type="text" class="form-control">
                                     
                                    </div>
                                   
                                </div>
                               <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Type de Compte:</label>
                                        <select class="form-select" wire:model="is_demo">
                                            <option value="0">Payé</option>
                                            <option value="1">En Démo</option>
                                            <option value="2">Test</option>
                                        </select>
                                        <small class="form-text text-muted">Le statut du compte est géré automatiquement.</small>
                                    </div>
                                </div>
                                <div class="row" style=" padding-top: 15px !important; ">
                                    <div class="col-md-6" style=" align-content: flex-end; align-items: flex-end; display: grid; ">
                                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Fermer</button>
                                    </div>
                                    <div class="col-md-6 align-content: end;align-items: flex-end;display: inline-grid;" style=" align-content: flex-end; align-items: flex-end; display: grid; ">
                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"> 
                                            <span wire:loading.remove wire:target="saveClient">
                                                {{ $editMode ? 'Modifier' : 'Ajouter' }}
                                            </span>
                                            <span wire:loading wire:target="saveClient">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Enregistrement...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Modal -->
    @if ($showPaymentModal)
        <div class="modal"  style="display: flex ; flex-direction: row; flex-wrap: wrap; align-content: space-around;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Ajouter un Paiement</h1>
                        <button type="button" class="btn-close m-0" wire:click="closePaymentModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="savePayment">
                            <!-- File Input (Always Visible) -->
                            <div class="row mb-3">
                                <div class="col-md-12" style="margin-bottom:15px;">
                                    <label class="form-label">Capture:</label>
                                    <input 
                                        type="file" 
                                        class="form-control" 
                                        id="paymentFileInput" 
                                        name="paymentFile" 
                                        wire:model.live="paymentFile" 
                                        accept="image/*" 
                                    >
                                </div>
                            </div>
                            <div wire:loading wire:target="paymentFile" class="col-md-12 justify-content-center align-items-center" style="max-height: 200px; overflow: hidden; position: relative;">
                                <div id="paymentFileLoading" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p>Chargement de l'image...</p>
                                </div>
                            </div>

                            @if($paymentFile)
                            <div wire:loading.remove wire:target="paymentFile">
                                <div class="col-md-12 d-flex justify-content-center align-items-center" style="max-height: 200px; overflow: hidden; position: relative;">
                                    @if(is_string($paymentFile))
                                    <!-- Existing Image -->
                                    <img id="paymentFilePreview" src="{{ asset($paymentFile) }}" alt="Payment Image Preview" class="img-fluid" style="width: 100%; height: auto; object-fit: contain;">
                                    @else
                                        <!-- Newly Uploaded Image -->
                                        <img id="paymentFilePreview" src="{{ $paymentFile->temporaryUrl() }}" alt="Payment Image Preview" class="img-fluid" style="width: 100%; height: auto; object-fit: contain;">
                                    @endif
                                </div> 
                            </div>
                            @endif

                            <!-- Other form fields... -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Statut:</label>
                                    <select class="form-select" wire:model="paymentStatus" required>
                                        <option value="0">Vérification</option>
                                        <option value="1">Payé</option>
                                        <option value="2">Expiré</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closePaymentModal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Archive Confirmation Modal -->
    @if ($showArchiveModal)
        <div class="modal"  style="display: flex ; flex-direction: row; flex-wrap: wrap; align-content: space-around;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Archive Client</h1>
                        <button type="button" class="btn-close m-0" wire:click="closeArchiveModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to archive this client?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeArchiveModal">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="archiveClient">Archive</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Single Notification Modal -->
    @if ($showSingleNotificationModal)
        <div class="modal"  style="display: flex ; flex-direction: row; flex-wrap: wrap; align-content: space-around;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <form wire:submit.prevent="sendSingleNotification">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="selectedClientId" style="margin-bottom:10px;font-size:larger;">Apprenant</label>
                                    <select class="form-select" id="selectedClientId" wire:model="selectedClientId" style="border-radius:10px !important;font-size:14px !important;" required>
                                        <option value="">Sélectionnez un client</option>
                                        @foreach ($allClients as $client)
                                            <option value="{{ $client->id }}">{{ $client->firstname }} {{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedClientId') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                             <div class="row mb-3">
                                <div class="col-md-12">
                                     <label for="notificationTitle" style="margin-bottom:10px;font-size:larger;" >Titre de notification</label>
                                    <input type="text" max="30" class="form-control" id="notificationTitle" style="border-radius:10px !important;font-size:14px !important;" wire:model="notificationTitle" required placeholder="Tapez votre Titre ici">
                                    @error('notificationTitle') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                     <label for="notificationMessage" style="margin-bottom:10px;font-size:larger;" >Message de notification</label>
                                    <textarea class="form-control" id="notificationMessage" wire:model="notificationMessage" rows="5" required placeholder="Tapez votre message ici"></textarea>
                                    @error('notificationMessage') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top:none !important;">
                                <button type="button" class="btn btn-secondary" wire:click="closeSingleNotificationModal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Envoyer Notification</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Notification Modal -->
    @if ($showBulkNotificationModal)
        <div class="modal"  style="display: flex ; flex-direction: row; flex-wrap: wrap; align-content: space-around;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <form wire:submit.prevent="sendBulkNotification">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                     <label for="notificationTitle" style="margin-bottom:10px;font-size:larger;" >Titre de notification</label>
                                    <input type="text" max="40" class="form-control" id="notificationTitle" style="border-radius:10px !important;font-size:14px !important;" wire:model="notificationTitle" required placeholder="Tapez votre Titre ici">
                                    @error('notificationTitle') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                     <label for="notificationMessage" style="margin-bottom:10px;font-size:larger;" >Message de notification</label>
                                    <textarea class="form-control" id="notificationMessage" wire:model="notificationMessage" rows="5" required placeholder="Tapez votre message ici"></textarea>
                                    @error('notificationMessage') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top:none !important;">
                                <button type="button" class="btn btn-secondary" wire:click="closeBulkNotificationModal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Envoyer Notifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    @if ($showDeleteModal)
        <div class="modal" style="display: flex; flex-direction: row; flex-wrap: wrap; align-content: space-around; background-color: rgba(0, 0, 0, 0.6);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none;">
                        <h1 class="modal-title fs-5 text-danger">
                            <i class="ti ti-alert-triangle me-2"></i>Confirmation de Suppression
                        </h1>
                        <button type="button" class="btn-close m-0" wire:click="closeDeleteModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous absolument certain de vouloir supprimer définitivement l'apprenant suivant ?</p>
                        
                        {{-- Affiche le nom du client à supprimer pour éviter les erreurs --}}
                        <div class="alert alert-secondary text-center">
                            <strong class="text-danger">{{ $deleteClientName }}</strong>
                        </div>
                        
                        <p class="fw-bold mt-3">
                            <span class="text-danger">Cette action est irréversible.</span> Toutes les données associées à cet utilisateur (chats, paiements, notifications, etc.) seront également supprimées pour toujours.
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top: none;">
                        {{-- Bouton pour annuler l'action --}}
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            Annuler
                        </button>
                        
                        {{-- Bouton pour confirmer la suppression, avec un indicateur de chargement --}}
                        <button type="button" class="btn btn-danger" wire:click="deleteClient">
                            <span wire:loading.remove wire:target="deleteClient">
                                <i class="ti ti-trash me-1"></i>
                                Oui, Supprimer
                            </span>
                            <span wire:loading wire:target="deleteClient">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Suppression...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Clients Table -->
    <div class="table-responsive">
        <table class="table table-bottom-border list-table-data align-middle mb-0">
            <thead>
                <tr class="app-sort">
                    <th><input type="checkbox" class="form-check-input checkAll" name="checkAll" wire:change="checkAllChanged"></th>
                    <th class="d-none">ID</th>
                    <th class="sort" data-sort="profil" scope="col">Profile</th>
                    <th class="sort" data-sort="contact" scope="col">Contact</th>
                    <th class="sort" data-sort="date" scope="col">Date</th>
                    <th class="sort" data-sort="paiment" scope="col">Paiement</th>
                    <th class="sort" data-sort="etat" scope="col">État</th>
                    <th class="sort" data-sort="action" scope="col"></th>
                </tr>
            </thead>
            <tbody class="list" id="t-data">
                @foreach ($clients as $client)
                @php
                    $path_img_client = $client->path_photo;
                    if ($client->path_photo) {
                        if ($client->path_photo[0] === '/') {
                            $path_img_client = substr($client->path_photo, 1);
                        } else {
                            $path_img_client = $client->path_photo;
                        }
                    }
                @endphp
                    <tr>
                        <th scope="row"><input class="form-check-input mt-0 ms-2" type="checkbox" name="item" wire:model="selectedClients.{{ $client->id }}"></th>
                        <td class="profil d-flex align-items-center">
                            <!-- Image on the left -->
                            <div class="me-3">
                                <img src="https://maxskills.tn/{{ $path_img_client }}" alt="avatar" class="h-40 w-40 rounded-full" style="border-radius:50%;">
                            </div>
                            <!-- Text on the right -->
                            <div>
                                <strong style="color: #000;font-weight:500 !important">{{ $client->firstname . ' ' . $client->name }}</strong><br>
                                <span>{{ '#' . str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="contact">
                            <strong style="color:#000;font-weight:500 !important"><i class="ti ti-mail me-1"></i>{{ $client->email }}</strong><br>
                            <i class="ti ti-phone me-1"></i>{{ $client->phone }}
                        </td>
                        <td class="date">
                            <strong style="color:#000;font-weight:500 !important">{{ $client->created_at->format('Y-m-d') }}</strong><br>
                            {{ $client->created_at->format('H:i:s') }}
                        </td>
                        <td class="paiment">
                            <div class="d-flex align-items-center">
                                <div class="h-40 w-40 d-flex-center b-r-50 overflow-hidden bg-dark me-2" style="background-color: rgb(241 240 239) !important">
                                    @php
                                        $method_img_path = "assets/paiment/espece.png";
                                        $typePaiment = "Paiement Espèce";
                                        $method = "Espèce";

                                        if($client->payment_getways == "virement"){
                                            $method_img_path = "assets/paiment/virement.png";
                                            $typePaiment = "Virement Bancaire";
                                            $method = "virement";
                                        } elseif ($client->payment_getways == "d17") {
                                            $method_img_path = "assets/paiment/d17.png";
                                            $typePaiment = "D17";
                                            $method = "Paiement en ligne";
                                        } elseif ($client->payment_getways == "flouci") {
                                            $method_img_path = "assets/paiment/flouci.png";
                                            $typePaiment = "flouci";
                                            $method = "Paiement en ligne";
                                        } elseif ($client->payment_getways == "konnect") {
                                            $method_img_path = "assets/paiment/konnect.png";
                                            $typePaiment = "Konnect";
                                            $method = "Paiement en ligne";
                                        }
                                    @endphp
                                    <img src="{{ asset($method_img_path) }}" alt="image" class="img-fluid">
                                </div>
                                <p class="m-0">
                                    <strong style="color:#000;font-weight:500 !important">{{ $typePaiment }}</strong><br>
                                    {{ $method }}
                                </p>
                            </div>
                        </td>
                        <td class="etat" style="cursor: pointer;">
                            @include('livewire.partials.lient-status-badge', ['client' => $client])
                        </td>
                        <td class="action">
                            <div class="btn-group dropdown-icon-none" style="border-radius: 20%; border: solid #E8EAEE 1px;">
                                <a class="icon-btn dropdown-toggle px-2 py-1" role="button" data-bs-toggle="dropdown"
                                   data-bs-auto-close="true" aria-expanded="false" style="color: #000;">
                                    <i class="ti ti-dots"></i>
                                </a> 
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-center align-items-center" href="#" 
                                           wire:click="openModal(true, {{ $client->id }})" 
                                           style="color: #000; transition: background-color 0.3s ease, color 0.3s ease;" 
                                           onmouseover="this.style.backgroundColor='#2970FF'; this.style.color='#fff';" 
                                           onmouseout="this.style.backgroundColor=''; this.style.color='#000';">
                                            Modifier
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-center align-items-center" href="#" 
                                           wire:click="openSingleNotificationModal"
                                           style="color: #000; transition: background-color 0.3s ease, color 0.3s ease;" 
                                           onmouseover="this.style.backgroundColor='#2970FF'; this.style.color='#fff';" 
                                           onmouseout="this.style.backgroundColor=''; this.style.color='#000';">
                                            Notification
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-center align-items-center" href="#" 
                                           wire:click="confirmArchive({{ $client->id }})" 
                                           style="color: #000; transition: background-color 0.3s ease, color 0.3s ease;" 
                                           onmouseover="this.style.backgroundColor='#2970FF'; this.style.color='#fff';" 
                                           onmouseout="this.style.backgroundColor=''; this.style.color='#000';">
                                            Archiver
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li> 
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-center align-items-center text-danger" href="#" 
                                        wire:click="confirmDelete({{ $client->id }})"
                                        style="transition: background-color 0.3s ease, color 0.3s ease;" 
                                        onmouseover="this.style.backgroundColor='#f8d7da';" 
                                        onmouseout="this.style.backgroundColor='';">
                                            <i class="ti ti-trash me-1"></i>
                                            Supprimer
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="list-pagination mt-4">
            <div class="pagination-nav d-flex justify-content-end" style="height:100% !important;">
                <ul class="pagination pagination-lg">
                    <!-- Previous Button -->
                    <li class="page-item {{ $clients->onFirstPage() ? 'disabled' : '' }}">
                        <button type="button" class="page-link" wire:click="previousPage" aria-label="Previous">
                            <span aria-hidden="true"><i class="ti ti-math-lower"></i></span>
                        </button>
                    </li>
        
                    <!-- Page Numbers -->
                    @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                        <li class="page-item {{ $clients->currentPage() === $page ? 'active' : '' }}">
                            <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                                {{ $page }}
                            </button>
                        </li>
                    @endforeach
        
                    <!-- Next Button -->
                    <li class="page-item {{ $clients->hasMorePages() ? '' : 'disabled' }}>
                        <button type="button" class="page-link" wire:click="nextPage" aria-label="Next">
                            <span aria-hidden="true"><i class="ti ti-math-greater"></i></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="alert alert-success text-center alert-new-client" role="alert" style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);background-color: #30B26B !important;z-index:99999 !important;--bs-alert-padding-y:0.1rem !important;">
            <p class="fw-bold f-s-15"><i class="ti ti-circle-check me-2"></i>{{ session('success') }}</p>
        </div>
        <script>
            setTimeout(() => {
                console.log('Dispatching clear-flash-client event');
                Livewire.dispatch('clear-flash-client'); 
            }, 500);
        </script>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', function () {
        const checkAll = document.querySelector('.checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('input[name="item"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                Livewire.dispatch('checkAllChanged');
            });

            // Sync checkAll state with individual checkboxes
            const checkboxes = document.querySelectorAll('input[name="item"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    checkAll.checked = allChecked;
                });
            });
        }

        // Initialize Select2 for client dropdown
        $('select[wire\\:model="selectedClientId"]').select2();
        $('select[wire\\:model="selectedClientId"]').on('change', function (e) {
            Livewire.dispatch('set', { selectedClientId: $(this).val() });
        });
    });
</script>