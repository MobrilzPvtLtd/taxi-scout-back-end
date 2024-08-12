<?php

namespace App\Mail\Driver;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DriverCreateByCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
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
        $data = $this->data;

        $subject = 'Your account create succussfull';
        $approve = '';
        if(isset($data['approve'])) {
            if($data['approve'] == 1){
                $approve = 'Approved';
            } elseif($data['approve'] == 0){
                $approve = 'Disapproved';
            } else{
                $approve = 'Pending';
            }
        } else {
            $approve = 'Pending';
        }

        return $this->view('email.driverCreateByCompanyMail', ['data' => $data, 'approve' => $approve])->subject($subject);
    }
}
