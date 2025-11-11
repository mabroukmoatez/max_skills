<?php

namespace App\Livewire;
 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Payments;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Notification;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Clients extends Component
{
    use WithPagination, WithFileUploads;

    // Propriétés de base
    public $photo_client, $existingPhoto;
    protected $paginationTheme = 'bootstrap';

    // Propriétés pour la recherche et les filtres unifiés
    #[Url(as: 'search', keep: true)]
    public $search = '';

    #[Url(keep: true)]
    public $filterState = '';

    public $showFilters = false;

    // Propriétés pour les modales
    public $isModalOpen = false, $editMode = false, $clientId;
    public $firstname, $name, $email, $phone, $location, $payment_getways, $password;
    public $is_demo; // Pour le type de compte

    // Propriétés pour l'archivage
    public $showArchiveModal = false, $archiveClientId;

    // Propriétés pour la suppression
    public $showDeleteModal = false, $deleteClientId, $deleteClientName;

    // Propriétés pour les notifications
    public $showSingleNotificationModal = false, $showBulkNotificationModal = false;
    public $notificationTitle, $notificationMessage, $selectedClients = [];
    public $selectedClientId;

    // Propriétés pour les paiements
    public $showPaymentModal = false, $paymentMethod, $paymentStatus, $paymentFile;

    // Propriétés diverses
    public $errorMessage = '', $nb_client;
    public $loading = false;
    public $randomAvatars = [
        '/storage/avatars/avatar-1.png',
        '/storage/avatars/avatar-2.png',
        '/storage/avatars/avatar-5.png',
        '/storage/avatars/avatar-6.png',
        '/storage/avatars/avatar-8.png',
        '/storage/avatars/avatar-9.png',
        '/storage/avatars/avatar-10.png',
        '/storage/avatars/avatar-11.png',
        '/storage/avatars/avatar-12.png',
        '/storage/avatars/avatar-13.png',
        '/storage/avatars/avatar-15.png',
        '/storage/avatars/avatar-16.png',
        '/storage/avatars/avatar-18.png',
        '/storage/avatars/avatar-19.png',
        '/storage/avatars/avatar-21.png',
        '/storage/avatars/avatar-22.png',
        '/storage/avatars/avatar-23.png',
        '/storage/avatars/avatar-24.png',
        '/storage/avatars/avatar-25.png',
        '/storage/avatars/avatar-26.png',
        '/storage/avatars/avatar-27.png',
        '/storage/avatars/avatar-28.png',
        '/storage/avatars/avatar-29.png',
        '/storage/avatars/avatar-31.png',
        '/storage/avatars/avatar-32.png',
        '/storage/avatars/avatar-33.png',
        '/storage/avatars/avatar-34.png',
        '/storage/avatars/avatar-35.png',
        '/storage/avatars/avatar-36.png',
        '/storage/avatars/avatar-38.png',
        '/storage/avatars/avatar-39.png',
        '/storage/avatars/avatar-41.png',
        '/storage/avatars/avatar-42.png',
        '/storage/avatars/avatar-44.png',
        '/storage/avatars/avatar-45.png',
        '/storage/avatars/avatar-47.png',
        '/storage/avatars/avatar-48.png',
        '/storage/avatars/avatar-49.png',
        '/storage/avatars/avatar-50.png',
        '/storage/avatars/avatar-51.png',
        '/storage/avatars/avatar-53.png',
        '/storage/avatars/avatar-54.png',
        '/storage/avatars/avatar-55.png',
        '/storage/avatars/avatar-56.png',
        '/storage/avatars/avatar-57.png',
        '/storage/avatars/avatar-58.png',
        '/storage/avatars/avatar-59.png',
        '/storage/avatars/avatar-60.png',
        '/storage/avatars/avatar-61.png',
        '/storage/avatars/avatar-62.png',
        '/storage/avatars/avatar-63.png',
        '/storage/avatars/avatar-64.png',
        '/storage/avatars/avatar-65.png',
        '/storage/avatars/avatar-66.png',
        '/storage/avatars/avatar-67.png',
        '/storage/avatars/avatar-69.png',
        '/storage/avatars/avatar-70.png',
        '/storage/avatars/avatar-71.png',
        '/storage/avatars/avatar-72.png',
        '/storage/avatars/avatar-73.png',
        '/storage/avatars/avatar-74.png',
        '/storage/avatars/avatar-75.png',
        '/storage/avatars/avatar-76.png',
        '/storage/avatars/avatar-77.png',
        '/storage/avatars/avatar-79.png',
        '/storage/avatars/avatar-81.png',
        '/storage/avatars/avatar-82.png',
        '/storage/avatars/avatar-83.png',
        '/storage/avatars/avatar-85.png',
        '/storage/avatars/avatar-86.png',
        '/storage/avatars/avatar-87.png',
        '/storage/avatars/avatar-91.png',
        '/storage/avatars/avatar-92.png',
        '/storage/avatars/avatar-93.png',
        '/storage/avatars/avatar-94.png',
        '/storage/avatars/avatar-96.png',
        '/storage/avatars/avatar-97.png',
        '/storage/avatars/avatar-98.png',
        '/storage/avatars/avatar-99.png',
        '/storage/avatars/avatar-100.png',
    ];
    public $statusCounts = [];
    
    #[Url(keep: true)] 
    public $filterGoogle = ''; 

    public function mount()
    {
        if (request()->has('openModal')) {
            $this->openModal(false);
        }
        $this->nb_client = User::where('role', 'client')->where('status', '!=', 2)->count();
    }


    public function updatedPhotoClient()
    {
        $this->loading = true;
        sleep(5);
        $this->loading = false;
    }

    public function clearFilePreview()
    {
        $this->reset('paymentFile');
        $this->dispatch('clear-file-preview');
    }

    #[On('clear-flash-client')]
    public function clearFlash()
    {
        Session::forget('success');
    }

    // --- Méthodes pour les Modales (open, close, reset) ---
    public function openModal($editMode = false, $clientId = null)
    {
        $this->isModalOpen = true;
        $this->editMode = $editMode;
        if ($editMode && $clientId) {
            $client = User::find($clientId);
            $this->clientId = $clientId;
            $this->firstname = $client->firstname;
            $this->name = $client->name;
            $this->email = $client->email;
            $this->phone = $client->phone;
            $this->location = $client->location;
            $this->payment_getways = $client->payment_getways;
            $this->existingPhoto = $client->path_photo;
            $this->is_demo = $client->is_demo;
            $this->password = '';
            $this->photo_client = null;
        } else {
            $this->resetForm();
        }
        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->dispatch('modal-closed');
    }

    public function resetForm()
    {
        $this->reset(['firstname', 'name', 'email', 'phone', 'location', 'password', 'photo_client', 'existingPhoto', 'clientId', 'errorMessage']);
        $this->editMode = false;
        $this->payment_getways = 'espece';
        $this->is_demo = 1; // Un nouveau compte est "En Démo" par défaut
    }

    // Save or update a client
    public function saveClient()
    {
        $this->validate([
            'firstname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->clientId,
            'phone' => 'nullable|string|min:8|max:8|regex:/^[2,9,5,3,4]/|unique:users,phone,' . $this->clientId,
            'is_demo' => 'required|in:0,1,2',
        ]);

        try {
            $status_value = ($this->is_demo == 0) ? 1 : 0;

            $data = [
                'firstname' => $this->firstname,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'location' => $this->location,
                'payment_getways' => $this->payment_getways,
                'is_demo' => $this->is_demo,
                'status' => $status_value,
                'role' => 'client',
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->editMode) {
                $user = User::find($this->clientId);
                if ($this->photo_client) {
                    $path = $this->photo_client->store('photo_client', 'public');
                    $data['path_photo'] = '/storage/' . $path;
                }
                $user->update($data);
                Session::flash('success', 'L’apprenant a été modifié avec succès');
            } else {
                if ($this->photo_client) {
                    $path = $this->photo_client->store('photo_client', 'public');
                    $data['path_photo'] = '/storage/' . $path;
                } else {
                    $data['path_photo'] = $this->randomAvatars[array_rand($this->randomAvatars)];
                }
                User::create($data);
                Session::flash('success', 'L’apprenant a été ajouté avec succès');
            }

            $this->nb_client = User::where('role', 'client')->where('status', '!=', 2)->count();
            $this->dispatch('nb-client-updated', nb_client: $this->nb_client);
            $this->closeModal();

        } catch (\Exception $e) {
            $this->errorMessage = 'Une erreur s\'est produite : ' . $e->getMessage();
        }
    }

    // Open the archive confirmation modal
    public function confirmArchive($clientId)
    {
        $this->showArchiveModal = true;
        $this->archiveClientId = $clientId;
    }

    // Archive a client (set status to 2)
    public function archiveClient()
    {
        try {
            User::find($this->archiveClientId)->update(['status' => 2]);
            Session::flash('success', 'Client archived successfully.');
            $this->nb_client = User::where('role', 'client')->where('status', '!=', 2)->count();
            $this->dispatch('nb-client-updated', nb_client: $this->nb_client);

            $this->showArchiveModal = false;
        } catch (\Exception $e) {
            Session::flash('error', 'An error occurred while archiving the client. Please try again.');
        }
    }

    // Close the archive confirmation modal
    public function closeArchiveModal()
    {
        $this->showArchiveModal = false;
        $this->archiveClientId = null;
    }

    // Payments
    public function openPaymentModal($clientId)
    {
        $this->selectedClientId = $clientId;
        $client = User::with('lastPayment')->find($clientId);
        $this->paymentMethod = $client->payment_getways;

        if ($client->lastPayment) {
            $this->paymentStatus = $client->lastPayment->status;
            if (is_string($client->lastPayment->img_path)) {
                $this->paymentFile = $client->lastPayment->img_path;
            } else {
                $this->paymentFile = null;
            }
        } else {
            $this->paymentStatus = 0;
            $this->paymentFile = null;
        }
        $this->showPaymentModal = true;
        $this->dispatch('open-payment-modal', nb_client: $this->nb_client);
    }

    public function savePayment()
    {
        $this->validate([
            'paymentStatus' => 'required|in:0,1,2',
            'paymentFile' => 'nullable|file|max:10240',
        ]);

        try {
            $client = User::find($this->selectedClientId);
            if($this->paymentStatus == 1) {
                $client->status = 1;
                $client->is_demo = 0;
                $client->save();
            } else {
                $client->status = 0;
                $client->is_demo = 1;
                $client->save();
            }
            $paymentData = [
                'methode' => $client->payment_getways,
                'status' => $this->paymentStatus,
                'user_id' => $this->selectedClientId,
            ];

            if ($this->paymentFile) {
                $path_photo_payment = $this->paymentFile->store('payments', 'public');
                $paymentData['img_path'] = '/storage/' . $path_photo_payment;
            }

            Payments::updateOrCreate(
                ['user_id' => $this->selectedClientId],
                $paymentData
            );

            Session::flash('success', 'Le paiement a été enregistré avec succès');
            $this->showPaymentModal = false;
        } catch (\Exception $e) {
            $this->errorMessage = 'Une erreur s’est produite lors de l’enregistrement du paiement. Veuillez réessayer.';
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->paymentMethod = null;
        $this->paymentStatus = null;
        $this->paymentFile = null;
        $this->selectedClientId = null;
    }

    public function resetSearch()
    {
        $this->reset('search');
    }

    // Notifications
    public function openSingleNotificationModal()
    {
        $this->resetNotificationFields();
        if ($this->areClientsSelected()) {
            $this->showBulkNotificationModal = true;
        } else {
            $this->showSingleNotificationModal = true;
        }
    }

    public function openBulkNotificationModal()
    {
        $this->resetNotificationFields();
        $this->showBulkNotificationModal = true;
    }

    public function closeSingleNotificationModal()
    {
        $this->showSingleNotificationModal = false;
        $this->resetNotificationFields();
    }

    public function closeBulkNotificationModal()
    {
        $this->showBulkNotificationModal = false;
        $this->resetNotificationFields();
    }

    public function sendSingleNotification()
    {
        $this->validate([
            'selectedClientId' => 'required|exists:users,id',
            'notificationTitle' => 'required|string|max:40',
            'notificationMessage' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            $this->errorMessage = 'User is not authenticated.';
            return;
        }

        Notification::create([
            'sender_id' => Auth::id(),
            'reciver_id' => $this->selectedClientId,
            'title' => $this->notificationTitle,
            'message' => $this->notificationMessage,
            'status' => false,
        ]);

        $this->closeSingleNotificationModal();
        session()->flash('success', 'Notification envoyée avec succès.');
    }

    public function sendBulkNotification()
    {
        $this->validate([
            'notificationTitle' => 'required|string|max:40',
            'notificationMessage' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            $this->errorMessage = 'User is not authenticated.';
            return;
        }

        $clients = $this->areClientsSelected()
            ? User::whereIn('id', array_keys($this->selectedClients))->where('role', 'client')->get()
            : User::where('role', 'client')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->get();

        foreach ($clients as $client) {
            Notification::create([
                'sender_id' => Auth::id(),
                'reciver_id' => $client->id,
                'title' => $this->notificationTitle,
                'message' => $this->notificationMessage,
                'status' => false,
            ]);
        }

        $this->closeBulkNotificationModal();
        session()->flash('success', 'Notifications envoyées avec succès.');
    }

    private function areClientsSelected()
    {
        return !empty($this->selectedClients);
    }

    public function checkAllChanged()
    {
        $clients = User::where('role', 'client')
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%')
                    ->orWhere('created_at', 'like', '%' . $this->search . '%')
                    ->orWhere('payment_getways', 'like', '%' . $this->search . '%');
            })
            ->pluck('id')
            ->toArray();

        $this->selectedClients = $this->areClientsSelected() ? [] : array_fill_keys($clients, true);
    }

    private function resetNotificationFields()
    {
        $this->selectedClientId = null;
        $this->notificationTitle = 'Tapez votre Titre ici';
        $this->notificationMessage = '';
    }



    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset('search', 'filterState', 'filterGoogle');
        $this->resetPage();
    }

    
    // Assurez-vous que les méthodes confirmDelete et closeDeleteModal existent toujours
    public function confirmDelete($clientId)
    {
        $client = User::find($clientId);
        if ($client) {
            $this->deleteClientId = $client->id;
            $this->deleteClientName = $client->firstname . ' ' . $client->name;
            $this->showDeleteModal = true;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteClientId = null;
        $this->deleteClientName = null;
    }
      public function deleteClient()
    {
        if (!$this->deleteClientId) {
            $this->closeDeleteModal();
            return;
        }
        DB::beginTransaction();
        try {
            $client = User::find($this->deleteClientId);
            if ($client) {
                Payments::where('user_id', $client->id)->delete();
                Chat::where('user_id', $client->id)->delete();
                Message::where('sender_id', $client->id)->delete();
                Notification::where('sender_id', $client->id)->orWhere('reciver_id', $client->id)->delete();

                $client->delete();
                DB::commit();
                Session::flash('success', 'L’apprenant et toutes ses données ont été supprimés avec succès.');
                $this->nb_client = User::where('role', 'client')->where('status', '!=', 2)->count();
                $this->dispatch('nb-client-updated', nb_client: $this->nb_client);
            } else {
                DB::rollBack();
                Session::flash('error', 'L\'utilisateur n\'a pas été trouvé.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Une erreur est survenue lors de la suppression.');
        } finally {
            $this->closeDeleteModal();
        }
    }

public function render()
{
    $this->statusCounts = [
        'actif'   => User::where('role', 'client')->where('is_demo', 0)->where('status', 1)->count(),
        'demo'    => User::where('role', 'client')->where('is_demo', 1)->count(),
        'test'    => User::where('role', 'client')->where('is_demo', 2)->count(),
        'expire'  => User::where('role', 'client')->where('is_demo', 0)->whereHas('lastPayment', fn($q) => $q->where('status', 2))->count(),
        'archive' => User::where('role', 'client')->where('status', 2)->count(),
        'avec_google' => User::where('role', 'client')->whereNotNull('google_id')->count(),
        'sans_google' => User::where('role', 'client')->whereNull('google_id')->count(),
    ];

    $clientsQuery = User::where('role', 'client')
        ->when($this->filterState, function ($query) {
            switch ($this->filterState) {
                case 'actif':
                    $query->where('is_demo', 0);
                    $query->where('status', 1);
                    break;
                case 'demo':
                    $query->where('is_demo', 1);
                    break;
                case 'test':
                    $query->where('is_demo', 2);
                    break;
                case 'expire':
                    $query->where('is_demo', 0)->whereHas('lastPayment', fn($q) => $q->where('status', 2));
                    break;
                case 'archive':
                    $query->where('status', 2);
                    break;
            }
        }, function ($query) {
            // S'exécute uniquement si $this->filterState est vide (comportement par défaut)
            $query->where('status', '!=', 2);
        })
         ->when($this->filterGoogle, function ($query) {
            if ($this->filterGoogle === 'avec') {
                $query->whereNotNull('google_id');
            } elseif ($this->filterGoogle === 'sans') {
                $query->whereNull('google_id');
            }
        })
        // Logique de recherche (à garder)
        ->when($this->search, function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        });

    $clients = $clientsQuery->with('lastPayment')->orderBy('id', 'desc')->paginate(10);
    $allClients = User::where('role', 'client')->where('status', '!=', 2)->orderBy('id', 'desc')->get();

    return view('livewire.clients', [
        'clients' => $clients,
        'nb_client' => $this->nb_client,
        'allClients' => $allClients,
    ]);
}

}