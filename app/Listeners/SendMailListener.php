<?php

namespace App\Listeners;

use App\Events\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendMail  $event
     * @return void
     */
    public function handle(SendMail $event)
    {
        $user = \App\User::find($event->userId)->toArray();
        \Mail::send('emails.mailEvent', $user, function($message) use ($user) {
            $message->to($user['email']);
            $message->subject('Event Testing');
        });
    }
}
