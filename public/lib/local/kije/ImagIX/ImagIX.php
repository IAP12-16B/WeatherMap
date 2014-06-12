<?php


namespace kije\ImagIX;

/**
 * Class for manipulating images. Uses the GD library.
 * @package kije\ImagIX
 */
class ImagIX
{
    private static $documents;


    public static function createDocument($name, $width, $height)
    {
        if (!array_key_exists($name, self::$documents)) {
            self::$documents[$name] = new Document($width, $height);
        } else {
            return self::$documents[$name];
        }
    }
} 