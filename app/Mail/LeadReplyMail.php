<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $replyMessage;
    public $subjectLine;
    public $customerName;

    public function __construct($replyMessage, $subjectLine, $customerName)
    {
        $this->replyMessage = $replyMessage;
        $this->subjectLine = $subjectLine;
        $this->customerName = $customerName;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.lead_reply');
    }
}