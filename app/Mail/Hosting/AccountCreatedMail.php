<?php
namespace App\Mail\Hosting;


use Spatie\MailTemplates\TemplateMailable;

class AccountCreatedMail extends TemplateMailable
{
    public $username;
    public $password;
    public $domain;
    public $cpanel_url;
    public $label;

    public function __construct($hosting)
    {
        $this->username = $hosting->username;
        $this->password = $hosting->password;
        $this->domain = $hosting->domain;
        $this->cpanel_url = $hosting->cpanel_url;
        $this->label = $hosting->label;
    }
}





