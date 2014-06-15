<?php


namespace kije\ImagIX;

/**
 * Class for manipulating images. Uses the GD library.
 * @package kije\ImagIX
 */
class ImagIX
{
    /**
     * @var Document[]
     */
    private static $documents;


    /**
     * @param $width
     * @param $height
     * @return Document
     */
    public static function createDocument($width, $height)
    {
        $name = sha1(uniqid() . rand());
        if (!array_key_exists($name, self::$documents)) {
            self::$documents[$name] = new Document($width, $height);
        } else {
            return self::$documents[$name];
        }
    }

    /**
     * @param $file
     * @return Document
     */
    public static function openDocument($file)
    {
        $name = sha1(uniqid() . rand());
        if (file_exists($file)) {
            self::$documents[$name] = Document::openFile($file);
            return self::$documents[$name];
        }
    }
}
