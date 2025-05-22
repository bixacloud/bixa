<?php

namespace App\Mail\Ticket;

use Spatie\MailTemplates\TemplateMailable;
use Coderflex\LaravelTicket\Models\Message;

class TicketReplyMail extends TemplateMailable
{
    public $ticket_id;
    public $title;
    public $reply;
    public $replier;

    public function __construct($ticket, Message $message)
    {
        $this->ticket_id = $ticket->id;
        $this->title = $ticket->title;
        $this->reply = $message->message;
        $this->replier = $message->user->name;
    }
}