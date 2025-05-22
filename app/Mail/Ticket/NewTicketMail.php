<?php

namespace App\Mail\Ticket;

use Spatie\MailTemplates\TemplateMailable;
use Coderflex\LaravelTicket\Models\Ticket;

class NewTicketMail extends TemplateMailable
{
    public $ticket_id;
    public $title;
    public $message;
    public $category;
    public $priority;
    public $service_type;
    public $service_info;

    public function __construct(Ticket $ticket)
    {
        $this->ticket_id = $ticket->id;
        $this->title = $ticket->title;
        $this->message = $ticket->messages->first()->message;
        $this->category = $ticket->category->name;
        $this->priority = $ticket->priority;
        $this->service_type = $ticket->service_type;
        
        // Get service info
        $this->service_info = $this->getServiceInfo($ticket);
    }

    protected function getServiceInfo($ticket)
    {
        if ($ticket->service_type === 'hosting') {
            $hosting = \DB::table('hosting_accounts')
                ->where('id', $ticket->service_id)
                ->first();
            return $hosting ? "Hosting: {$hosting->domain} ({$hosting->label})" : '';
        }

        if ($ticket->service_type === 'ssl') {
            $ssl = \DB::table('certificates')
                ->where('id', $ticket->service_id) 
                ->first();
            return $ssl ? "SSL: {$ssl->domain} ({$ssl->type})" : '';
        }

        return '';
    }
}