<?php


namespace kije\ImagIX\utils;


class Colors
{
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        $r = 0;
        $g = 0;
        $b = 0;

        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return array($r, $g, $b);
    }

    public static function rgb2hex($r, $g, $b, $prefix = '#', $a = false)
    {
        $hex = $prefix;
        $hex .= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

        if ($a !== false) {
            $hex .= str_pad(dechex($a), 2, "0", STR_PAD_LEFT);
        }

        return $hex;
    }
} 