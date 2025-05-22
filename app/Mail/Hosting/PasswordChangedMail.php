<?php
namespace App\Mail\Hosting;

use Spatie\MailTemplates\TemplateMailable;

class PasswordChangedMail extends TemplateMailable
{
    public $username;
    public $domain;
    public $new_password;
    public $cpanel_url;

    public function __construct($hosting, $new_password)
    {
        $this->username = $hosting->username;
        $this->domain = $hosting->domain;
        $this->new_password = $new_password;
        $this->cpanel_url = $hosting->cpanel_url;
    }
}