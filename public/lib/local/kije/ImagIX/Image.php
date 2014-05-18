<?php


namespace kije\ImagIX;

use kije\ImagIX\Exception\ImageException;
use kije\ImagIX\utils\Colors;

/**
 * Class to store a GD image.
 * @package kije\ImagIX
 */
class Image
{
    const IMAGE_TYPE_GIF = 'gif';
    const IMAGE_TYPE_JPEG = 'jpeg';
    const IMAGE_TYPE_PNG = 'png';
    const IMAGE_TYPE_WBMP = 'wbmp';
    const IMAGE_TYPE_GD2 = 'gd2';
    const IMAGE_TYPE_INTERLACED_JPEG = self::IMAGE_TYPE_JPEG;
    const IMAGE_TYPE_TRANSPARENT_PNG = self::IMAGE_TYPE_PNG;

    /**
     * @var resource
     */
    protected $gdImage;

    private $width;
    private $height;
    private $colors = array();

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width = 1, $height = 1)
    {
        $this->width = $width;
        $this->height = $height;
        $this->gdImage = imagecreatetruecolor($width, $height);
        imagefill($this->gdImage, 0, 0, $this->colorTransparent());
    }

    public function colorTransparent()
    {
        return $this->createColor(0xFF, 0xFF, 0xFF, 0x7F);
    }

    public function createColor($r, $g, $b, $a = false)
    {
        $colorKey = Colors::rgb2hex($r, $g, $b, '', $a);
        if (!array_key_exists($colorKey, $this->colors)) {
            if ($a === false) {
                $this->colors[$colorKey] = imagecolorallocate($this->gdImage, $r, $g, $b);
            } else {
                $this->colors[$colorKey] = imagecolorallocatealpha($this->gdImage, $r, $g, $b, $a);
            }
        }

        return $this->colors[$colorKey];
    }

    /**
     * @param $file
     * @return null|resource
     * @throws Exception\ImageException
     */
    public static function fromFile($file)
    {
        $return = null;
        if (is_file($file)) {
            switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                case self::IMAGE_TYPE_GIF:
                case self::IMAGE_TYPE_JPEG:
                case self::IMAGE_TYPE_PNG:
                case self::IMAGE_TYPE_WBMP:
                case self::IMAGE_TYPE_GD2:
                    $return = imagecreatefromstring(file_get_contents($file));
                    break;

                default:
                    throw new ImageException("Wrong file type provided!");
            }
        } else {
            throw new ImageException("Argument is not a file!");
        }

        return $return;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getGdImage()
    {
        return $this->gdImage;
    }

    /**
     * @param mixed $gd_image
     */
    public function setGdImage($gd_image)
    {
        $this->gdImage = $gd_image;
    }

    /**
     * @return bool|null
     */
    public function getJPEG()
    {
        return $this->toImage(self::IMAGE_TYPE_JPEG);
    }

    /**
     * @param string $filetype
     * @param null $filename
     * @param null $quality
     * @param null $filter
     * @throws Exception\ImageException
     * @return bool|null
     */
    public function toImage($filetype = self::IMAGE_TYPE_JPEG, $filename = null, $quality = null, $filter = null)
    {
        if ($filename && !is_writable($filename)) {
            throw new ImageException('Path is not writable!');
        }
        $return = null;
        if (!$filename) {
            ob_start();
        }
        switch ($filetype) {
            case self::IMAGE_TYPE_GIF:
                $return = imagegif($this->gdImage, $filename);
                break;

            case self::IMAGE_TYPE_INTERLACED_JPEG:
                imageinterlace($this->gdImage, true);
            // no break is intended
            case self::IMAGE_TYPE_JPEG:
                $return = imagejpeg($this->gdImage, $filename, $quality);
                break;

            case self::IMAGE_TYPE_TRANSPARENT_PNG:
                imagesavealpha($this->gdImage, true);
                imagecolortransparent($this->gdImage, $this->colorTransparent());
                imagealphablending($this->gdImage, true);
            // no break is intended
            case self::IMAGE_TYPE_PNG:
                $return = imagepng($this->gdImage, $filename, $quality, $filter);
                break;

            case self::IMAGE_TYPE_WBMP:
                $return = image2wbmp($this->gdImage, $filename);
                break;

            case self::IMAGE_TYPE_GD2:
                $return = imagegd2($this->gdImage, $filename);
                break;

            default:
                $return = false;
                break;
        }

        if (!$filename && $return) {
            $return = ob_get_contents();
            ob_end_clean();
        }

        return $return;
    }

    /**
     * @param $filename
     * @return bool|null
     */
    public function saveJPEG($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_JPEG, $filename);
    }

    /**
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function getPNG()
    {
        return $this->toImage(self::IMAGE_TYPE_PNG);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function savePNG($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_PNG, $filename);
    }

    /**
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function getPNG24()
    {
        return $this->toImage(self::IMAGE_TYPE_TRANSPARENT_PNG);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function savePNG24($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_TRANSPARENT_PNG, $filename);
    }

    /**
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function getGIF()
    {
        return $this->toImage(self::IMAGE_TYPE_GIF);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\ImageException
     */
    public function saveGIF($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_GIF, $filename);
    }

    public function __destruct()
    {
        imagedestroy($this->gdImage);
    }

    public function crop($width, $height)
    {

    }

    public function setBackground($color)
    {

    }

    public function drawLine()
    {

    }

    public function drawArc()
    {

    }

    public function fill()
    {

    }

    public function drawEllipse()
    {

    }

    public function drawPolygon()
    {

    }

    public function drawRectangle()
    {

    }

    public function filter()
    {

    }

    public function flip()
    {

    }

    public function drawPixel()
    {

    }

    public function drawChar()
    {

    }

    public function colorWhite()
    {
        return $this->createColor(0xFF, 0xFF, 0xFF);
    }

    public function colorBlack()
    {
        return $this->createColor(0x00, 0x00, 0x00);
    }

    public function colorRed()
    {
        return $this->createColor(0xFF, 0x00, 0x00);
    }

    public function colorGreen()
    {
        return $this->createColor(0x00, 0xFF, 0x00);
    }

    public function colorBlue()
    {
        return $this->createColor(0x00, 0x00, 0xFF);
    }

    public function colorYellow()
    {
        return $this->createColor(0xFF, 0xFF, 0x00);
    }

    public function colorMagenta()
    {
        return $this->createColor(0xFF, 0x00, 0xFF);
    }

    public function colorCyan()
    {
        return $this->createColor(0x00, 0xFF, 0xFF);
    }

    protected function prepareDraw($color, $thickness)
    {

    }
}
