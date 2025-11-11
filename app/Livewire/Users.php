<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Livewire\Attributes\On; 

class Users extends Component
{
    use WithPagination;

    public $firstname;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $role = 'agent';
    public $niveau;
    public $language = 'fr';
    public $status = 1;
    public $path_photo;
    public $userId; // To store the ID of the user being edited

    public $showModal = false;
    public $isEditMode = false; // To toggle between create and edit modals

    public $randomAvatars = [
        'assets/images/ai_avtar/1.jpg',
        'assets/images/ai_avtar/2.jpg',
        'assets/images/ai_avtar/3.jpg',
        'assets/images/ai_avtar/4.jpg',
        'assets/images/ai_avtar/5.jpg',
        'assets/images/ai_avtar/6.jpg',
        'assets/images/ai_avtar/7.png',
    ];

    protected $rules = [
        'firstname' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|string|min:8',
        'niveau' => 'required|string|max:255',
        'status' => 'required|integer|in:0,1',
    ];

    public function render()
    {
        $users = User::whereIn('role', ['admin', 'agent'])
            ->orderBy('role', 'asc')
            ->paginate(10);

        return view('livewire.users', [
            'users' => $users,
        ]);
    }

    public function openModal()
    {
        $this->isEditMode = false;
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditMode = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->firstname = '';
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->niveau = '';
        $this->status = 1;
        $this->path_photo = $this->randomAvatars[array_rand($this->randomAvatars)];
        $this->userId = null;
        $this->resetErrorBag(); // Clear validation errors
    }

    public function createAgent()
    {
        try {
            $this->validate([
                'firstname' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'phone' => 'required|string|unique:users,phone|max:15',
                'password' => 'required|string|min:8',
                'niveau' => 'required|string',
                'path_photo' => 'nullable|string',
            ], [
                'firstname.required' => 'The first name is required.',
                'name.required' => 'The last name is required.',
                'email.required' => 'The email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already taken.',
                'phone.required' => 'The phone number is required.',
                'phone.unique' => 'This phone number is already in use.',
                'password.required' => 'The password is required.',
                'password.min' => 'The password must be at least 8 characters.',
                'niveau.required' => 'The level is required.',
            ]);

            User::create([
                'firstname' => $this->firstname,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'niveau' => $this->niveau,
                'language' => $this->language,
                'status' => $this->status,
                'path_photo' => $this->path_photo,
            ]);

            $this->closeModal();
            session()->flash('message', 'Agent created successfully.');
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                $errorCode = $e->getCode();
                if ($errorCode == 1062) {
                    if (stripos($e->getMessage(), 'email') !== false) {
                        $this->addError('general', 'The email address is already taken.');
                    } elseif (stripos($e->getMessage(), 'phone') !== false) {
                        $this->addError('general', 'The phone number is already in use.');
                    } else {
                        $this->addError('general', 'A duplicate entry occurred.');
                    }
                } else {
                    $this->addError('general', 'An error occurred while creating the agent.');
                }
            } else {
                $this->addError('general', 'Agent creation failed: ' . $e->getMessage());
            }
            $this->closeModal();
        }
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->firstname = $user->firstname;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->niveau = $user->niveau;
        $this->status = $user->status;
        $this->path_photo = $user->path_photo;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function updateAgent()
    {
        try {
            $this->validate([
                'firstname' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->userId,
                'phone' => 'required|string|unique:users,phone,' . $this->userId . '|max:15',
                'niveau' => 'required|string',
                'status' => 'required|integer|in:0,1',
            ], [
                'firstname.required' => 'The first name is required.',
                'name.required' => 'The last name is required.',
                'email.required' => 'The email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already taken.',
                'phone.required' => 'The phone number is required.',
                'phone.unique' => 'This phone number is already in use.',
                'niveau.required' => 'The level is required.',
            ]);

            $user = User::findOrFail($this->userId);
            $user->update([
                'firstname' => $this->firstname,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'niveau' => $this->niveau,
                'status' => $this->status,
                'path_photo' => $this->path_photo,
            ]);

            $this->closeModal();
            session()->flash('message', 'Agent updated successfully.');
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                $errorCode = $e->getCode();
                if ($errorCode == 1062) {
                    if (stripos($e->getMessage(), 'email') !== false) {
                        $this->addError('general', 'The email address is already taken.');
                    } elseif (stripos($e->getMessage(), 'phone') !== false) {
                        $this->addError('general', 'The phone number is already in use.');
                    } else {
                        $this->addError('general', 'A duplicate entry occurred.');
                    }
                } else {
                    $this->addError('general', 'An error occurred while updating the agent.');
                }
            } else {
                $this->addError('general', 'Agent update failed: ' . $e->getMessage());
            }
        }
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', $id);
    }

    #[On('deleteUser')]
    public function deleteUser($id)
    {  
        try {
            $user = User::findOrFail($id);
            if($user->role != 'admin'){
                $user->delete();
                session()->flash('message', 'Agent deleted successfully.');
            } else {
                $this->addError('general', 'An error occurred while deleting the Admin: ');
            }
           
        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while deleting the agent: ' . $e->getMessage());
        }
    }
}