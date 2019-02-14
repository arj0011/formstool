<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FormResubmitMail extends Mailable
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
        return $this->subject('Resubmit Form')->view('mails.resubmit_form_mail',compact($this->data));
    }
}
