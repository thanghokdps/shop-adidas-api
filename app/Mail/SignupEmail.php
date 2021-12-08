<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->mailData = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SignupEmail
    {
        $data = $this->mailData;
        if($data['send']=="welcome") {
            return $this->from('adidasshoeshoppbl6@gmail.com', 'Adidas Shoe Shop')->subject('Welcome!')->view('mail.welcome-email', ['data'=>$this->mailData]);
        } elseif ($data['send']=="forget") {
            return $this->from('adidasshoeshoppbl6@gmail.com', 'Adidas Shoe Shop')->subject('ForgetPassword!')->view('mail.thankyou-email', ['data'=>$this->mailData]);
        }
        return $this->view('view.name');
    }
}
