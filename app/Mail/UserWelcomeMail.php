<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Import the User model

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password; // Pass the generated password

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $password The randomly generated password for the user
     * @return void
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenue sur notre plateforme!')
                    ->view('emails.user_welcome'); // This points to resources/views/emails/user_welcome.blade.php
    }
}