<?php

namespace App\Mail\Ticket;

use Spatie\MailTemplates\TemplateMailable;
use Coderflex\LaravelTicket\Models\Ticket;

class TicketStatusChangedMail extends TemplateMailable
{
    public $ticket_id;
    public $title;
    public $old_status;
    public $new_status;

    public function __construct(Ticket $ticket, $oldStatus)
    {
        $this->ticket_id = $ticket->id;
        $this->title = $ticket->title;
        $this->old_status = $oldStatus;
        $this->new_status = $ticket->status;
    }
}