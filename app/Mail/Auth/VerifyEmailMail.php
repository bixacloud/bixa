<?php
namespace App\Mail\Auth;

use Spatie\MailTemplates\TemplateMailable;

class VerifyEmailMail extends TemplateMailable
{
    public $name;
    public $verification_url;

    public function __construct($user, $url)
    {
        $this->name = $user->name;
        $this->verification_url = $url;
    }
}


