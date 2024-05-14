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
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $token
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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
        $token = $this->token;
        $user = $this->user;

        return $this->view($view, ['token' => $token, 'app_url' => $app_url, 'user' => $user])
                    ->subject($subject);
    }
}
