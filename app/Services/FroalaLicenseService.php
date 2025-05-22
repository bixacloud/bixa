<?php

namespace App\Services;

class FroalaLicenseService
{
    /**
     * Calculate checksum for a value
     *
     * @param  mixed  $e
     * @return int
     */
    protected function calculateChecksum($e)
    {
        $sum = array_sum(str_split((string)$e));
        return ($sum > 10) ? ($sum % 9 + 1) : $sum;
    }

    /**
     * Encode text for Froala license key
     *
     * @param  string  $text
     * @return string
     */
    protected function encode($text)
    {
        static $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $o = 53;
        $result = '';
        
        foreach (str_split($text) as $char) {
            $a = $this->calculateChecksum(++$o);
            $s = ord($char) ^ (($o - 1) & 31);
            $result .= chr($s);
        }
        
        return $chars[53] . $result . '==';
    }

    /**
     * Generate a Froala Editor license key
     *
     * @param  string  $name
     * @param  int  $year
     * @return string
     */
    public function generateLicense($name, $year)
    {
        $licenseData = "V3|{$name}|WILDCARD_ACTIVATION_KEY|{$year}";
        return $this->encode($licenseData);
    }
}