<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $userLogin;
    public $userPassword;
    public $userType;

    public function __construct($user, $userPassword, $userType)
    {
        //
        $this->userType = $userType;
        $this->userLogin = $user->email;
        $this->userPassword = $userPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Your User Credentials';
        if($this->userType == 'admin')
        {
            $subject = 'New User Credentials';
        }
        return $this->subject($subject)->view('userconfirmation');
    }
}
