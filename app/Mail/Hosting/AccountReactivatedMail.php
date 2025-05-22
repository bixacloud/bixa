<?php
namespace App\Mail\Hosting;

use Spatie\MailTemplates\TemplateMailable;


class AccountReactivatedMail extends TemplateMailable
{
    public $username;
    public $domain;
    public $label;
    public $cpanel_url;

    public function __construct($hosting)
    {
        $this->username = $hosting->username;
        $this->domain = $hosting->domain;
        $this->label = $hosting->label;
        $this->cpanel_url = $hosting->cpanel_url;
    }
}