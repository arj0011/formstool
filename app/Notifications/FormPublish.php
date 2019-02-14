<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FormPublish extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notify = array();

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notify = array())
    {  
        $this->notify = $notify;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    { 
        $url = url('create-data?id='.$this->notify->form_id);

        return (new MailMessage)->greeting('Hello! '.$this->notify->first_name)
                ->line('You have a assign a form template.')
                ->line('To find template by this link')
                ->action('View Template', $url);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
