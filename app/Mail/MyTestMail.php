<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class MyTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $user;
    public $password;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->user = DB::table('users')->where('id', $this->details)->first();
         $data = [
            'user' => $this->user
        ];
        return $this->subject('Mail from larafinance')
                    ->view('mytestmail', $data);
    }
}