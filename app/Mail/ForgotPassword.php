<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user resetting the password.
     *
     * @var User
     */
    protected $user;

    /**
     * The password reset token.
     *
     * @var string
     */
    protected $otp;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $otp
     */
    public function __construct(User $user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Password Reset Request';
        $view = 'email.auth.password.reset';
        $app_url = url()->current();
        $otp = $this->otp;
        $user = $this->user;

        return $this->view($view, ['otp' => $otp, 'app_url' => $app_url, 'user' => $user])
                    ->subject($subject);
    }
}
