<?php

namespace App\Helpers;

class GravatarHelper
{
    /**
     * Get Gravatar URL for the given email address
     *
     * @param string $email
     * @param int $size Size in pixels
     * @param string $default Default image style (mp=mystery person, identicon, monsterid, wavatar, retro, robohash, blank)
     * @param string $rating Rating (g, pg, r, x)
     * @return string
     */
    public static function url($email, $size = 80, $default = 'mp', $rating = 'g')
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}&r={$rating}";
    }
}