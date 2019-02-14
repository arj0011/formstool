<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class FormScheduleMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $data = array();


    /**
     * Create a new message instance.
     *
     * @return void
     */
    
      public function __construct(Array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Schedule Form')->view('mails.schedule_form_mail_to_user',compact($this->data));
    }
}
