<?php

namespace App\Mail;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $refund;
    public $status;

    /**
     * Create a new message instance.
     *
     * @param  Refund  $refund
     * @param  string  $status
     * @return void
     */
    public function __construct(Refund $refund, string $status)
    {
        $this->refund = $refund;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Refund Processed: ' . $this->status)
            ->view('emails.refund_processed'); // This will point to the Blade view
    }
}
