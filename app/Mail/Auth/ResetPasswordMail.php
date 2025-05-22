<?php
namespace App\Mail\Auth;

use Spatie\MailTemplates\TemplateMailable;

class ResetPasswordMail extends TemplateMailable
{
    public $name;
    public $reset_url;

    public function __construct($user, $url)
    {
        $this->name = $user->name;
        $this->reset_url = $url;
    }
}