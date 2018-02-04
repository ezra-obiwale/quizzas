<?php
namespace App\Mail;

use App\Entities\V1\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmationAccount extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.message', $this->createEmailArray())
            ->subject('Confirm your ' . config('app.name') . ' account, ' . $this->user->name);
    }

    public function createEmailArray()
    {
        return [
            'greeting' => 'Welcome ' . $this->user->name . ',',
            'introLines' => [
                'Thanks for signing up!',
                'To start using your ' . config('app.name') . ' account, all you have to do is confirm your email address.'
            ],
            'actionText' => 'Confirm Now',
            'actionUrl' => url("signup/confirm/{$this->user->token}"),
            'salutation' => "Cheers,<br />" . config('app.name')
        ];
    }
}
