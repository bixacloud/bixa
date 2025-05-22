<?php
namespace App\Mail\Hosting;

use Spatie\MailTemplates\TemplateMailable;

class AccountDeactivatedMail extends TemplateMailable
{
    public $username;
    public $domain;
    public $label;
    public $reason;

    public function __construct($hosting, $reason)
    {
        $this->username = $hosting->username;
        $this->domain = $hosting->domain;
        $this->label = $hosting->label;
        $this->reason = $reason;
    }
}