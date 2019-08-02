<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseOrder extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     * @param order
     * @return void
     */

    public $order;

    public function __construct($order)
    {
        //
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        return $this->from(Auth::user())->view('purchaseorder');
        return $this->from(env('MAIL_COMPANY_MASTER'))->view('purchaseorder');
    }
}
