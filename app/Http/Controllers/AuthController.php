<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
 
class AuthController extends Controller
{
    public $randomAvatars = [
        '/storage/avatars/avatar-1.png',
        '/storage/avatars/avatar-2.png',
        '/storage/avatars/avatar-5.png',
        '/storage/avatars/avatar-6.png',
        '/storage/avatars/avatar-7.png',
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
    
    public function showLoginFormClient()
    {
        return view('client.auth.login');
    }
    //google
      /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();
            
            $randomAvatar = $this->randomAvatars[array_rand($this->randomAvatars)];

            if ($user) {
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId()
                    ]);
                }
                if (!$user->email_verified_at) {
                    $user->update([
                        'email_verified_at' => now()
                    ]);
                }
                if (!$user->path_photo) {
                    $user->update([
                        'path_photo' => $randomAvatar
                    ]);
                }
            } else {
                $googleUserData = $googleUser->user;

                $user = User::create([
                    'email'             => $googleUser->getEmail(),
                    'firstname'         => $googleUserData['given_name'],
                    'name'              => $googleUserData['family_name'],
                    'google_id'         => $googleUser->getId(),
                    'password'          => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'path_photo' => $randomAvatar,
                    'role'              => 'client',
                    'is_demo'           => 1,
                    'status'            => 1,
                ]);
            }


            Auth::login($user,true);

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.cours');
                case 'agent':
                    return redirect()->route('agent.cours');
                case 'client':
                    return redirect()->route('courById', ['id' => '9f705dc0-61fc-4b19-9b10-9fe097f618de']);
                default:
                    return redirect()->route('home');
            }

        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e);
            return redirect()->route('login')->withErrors(['email' => 'Unable to login using Google. Please try again.']);
        }
    }



    public function showLoginFormVerifyEmailClient()
    {
        return view('client.auth.verify-email');
    }
    public function showLoginFormForgotPasswordClient()
    {
        return view('client.auth.forgot-password');
    }
    public function showLoginFormResetPasswordClient()
    {
        return view('client.auth.reset-password');
    }
    // Show Registration Form 
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle Registration
    public function register(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'telephone' => 'required|string|max:8',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $randomAvatar = $this->randomAvatars[array_rand($this->randomAvatars)];

        $user = User::create([
            'firstname' => $request->prenom,
            'name' => $request->nom,
            'phone' => $request->telephone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'path_photo' => $randomAvatar,
            'role'              => 'client',
            'is_demo'           => 1,
            'status'            => 1,
        ]);

        Auth::login($user);

        return redirect('/formation/cour/9f705dc0-61fc-4b19-9b10-9fe097f618de');
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user_verif = User::where('email',$request->input('email'))->first();
        if($user_verif){
            if($user_verif->role === 'client'){
                if($user_verif->status === 2){
                    return redirect()->route('login')->withErrors(['email' => 'Votre compte est Archivée .',]);
                }
            } else {
                if($user_verif->status === 0){
                    return redirect()->route('login')->withErrors(['email' => 'Votre compte est désactivé .',]);
                }
            }
            
        }
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.cours');
                case 'agent':
                    return redirect()->route('agent.cours');
                case 'client':
                    return redirect()->route('courById', ['id' => '9f705dc0-61fc-4b19-9b10-9fe097f618de']);
                default:
                    return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Merci de vérifier vos informations.',
        ]);
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}