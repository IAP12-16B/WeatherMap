<?php


namespace kije\ImagIX;

use kije\ImagIX\Exception\CanvasException;
use kije\ImagIX\utils\Colors;

/**
 * Class to store a GD image.
 * @package kije\ImagIX
 */
class Canvas
{
    const IMAGE_TYPE_GIF = 0;
    const IMAGE_TYPE_JPEG = 1;
    const IMAGE_TYPE_PNG = 2;
    const IMAGE_TYPE_WBMP = 3;
    const IMAGE_TYPE_GD2 = 4;
    const IMAGE_TYPE_INTERLACED_JPEG = 5;
    const IMAGE_TYPE_TRANSPARENT_PNG = 6;
    const TEXT_ALIGNMENT_LEFT = 0;
    const TEXT_ALIGNMENT_CENTER = 1;
    const TEXT_ALIGNMENT_RIGHT = 2;
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
        $this->fill($this->colorTransparent()); // fill background
        imagesetinterpolation($this->gdImage, IMG_BICUBIC);
        imageantialias($this->gdImage, true);
    }

    /**
     * @param $color
     * @param int $startX
     * @param int $startY
     */
    public function fill($color, $startX = 0, $startY = 0)
    {
        imagefill($this->gdImage, $startX, $startY, $color);
    }

    /**
     * @return mixed
     */
    public function colorTransparent()
    {
        return $this->createColor(0xFF, 0xFF, 0xFF, 0x7F);
    }

    /**
     * @param $r
     * @param $g
     * @param $b
     * @param bool $a
     * @return mixed
     */
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
     * @return null|Canvas
     * @throws Exception\CanvasException
     */
    public static function fromFile($file)
    {
        $gdImage = null;
        if (is_file($file)) {
            switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                case 'gif':
                case 'jpeg':
                case 'jpg':
                case 'png':
                case 'wbmp':
                case 'gd2':
                    $gdImage = imagecreatefromstring(file_get_contents($file));
                    break;

                default:
                    throw new CanvasException("Wrong file type provided!");
            }
        } else {
            throw new CanvasException("Argument is not a file!");
        }

        $image_size = getimagesize($file);

        $canvas = new Canvas($image_size[0], $image_size[1]);
        $canvas->setGdImage($gdImage);
        return $canvas;
    }

    /**
     * @return int
     */
    public function &getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function &getWidth()
    {
        return $this->width;
    }

    /**
     * @return bool|null
     */
    public function getJPEG()
    {
        return $this->toImage(self::IMAGE_TYPE_JPEG, null, 0);
    }

    /**
     * @param int|string $filetype
     * @param null $filename
     * @param null $quality
     * @param null $filter
     * @throws Exception\CanvasException
     * @return bool|null
     */
    public function toImage($filetype = self::IMAGE_TYPE_JPEG, $filename = null, $quality = null, $filter = null)
    {
        if ($filename && !is_writable($filename)) {
            throw new CanvasException('Path is not writable!');
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
        return $this->toImage(self::IMAGE_TYPE_JPEG, $filename, 100);
    }

    /**
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function getPNG()
    {
        return $this->toImage(self::IMAGE_TYPE_PNG, null, 0);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function savePNG($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_PNG, $filename, 0);
    }

    /**
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function getPNG24()
    {
        return $this->toImage(self::IMAGE_TYPE_TRANSPARENT_PNG, null, 0);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function savePNG24($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_TRANSPARENT_PNG, $filename, 0);
    }

    /**
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function getGIF()
    {
        return $this->toImage(self::IMAGE_TYPE_GIF);
    }

    /**
     * @param $filename
     * @return bool|null
     * @throws Exception\CanvasException
     */
    public function saveGIF($filename)
    {
        return $this->toImage(self::IMAGE_TYPE_GIF, $filename);
    }


    public function destroy()
    {
        imagedestroy($this->gdImage);
        unset($this);
    }

    /**
     * @param $width
     * @param $height
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function crop($width, $height, $x = 0, $y = 0)
    {
        imagecrop(
            $this->gdImage,
            array(
                'x' => $x,
                'y' => $y,
                'width' => $width,
                'height' => $height
            )
        );

        return $this;
    }

    public function autocrop($mode = -1, $threshold = 0.5, $color = -1)
    {
        imagecropauto($this->gdImage, $mode, $threshold, $color);

        return $this;
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $color
     * @param bool $dashed
     * @return $this
     */
    public function drawLine($x1, $y1, $x2, $y2, $color, $dashed = false)
    {
        if ($dashed) {
            imagedashedline($this->gdImage, $x1, $y1, $x2, $y2, $color);
        } else {
            imageline($this->gdImage, $x1, $y1, $x2, $y2, $color);
        }

        return $this;
    }

    /**
     * @param $cx
     * @param $cy
     * @param $width
     * @param $height
     * @param $start
     * @param $end
     * @param $color
     * @param bool $filled
     * @return $this
     */
    public function drawArc($cx, $cy, $width, $height, $start, $end, $color, $filled = false)
    {
        if ($filled) {
            imagefilledarc($this->gdImage, $cx, $cy, $width, $height, $start, $end, $color, IMG_ARC_PIE);
        } else {
            imagearc($this->gdImage, $cx, $cy, $width, $height, $start, $end, $color);
        }


        return $this;
    }

    /**
     * @param $cx
     * @param $cy
     * @param $width
     * @param $height
     * @param $color
     * @return $this
     */
    public function drawEllipse($cx, $cy, $width, $height, $color)
    {
        imageellipse($this->gdImage, $cx, $cy, $width, $height, $color);
        // TODO: filled

        return $this;
    }

    /**
     * @param $points
     * @param $num_points
     * @param $color
     * @return $this
     */
    public function drawPolygon($points, $num_points, $color)
    {
        imagepolygon($this->gdImage, $points, $num_points, $color);
        // TODO: filled
        return $this;
    }

    /**
     * @param $filtertype
     * @param null $arg1
     * @param null $arg2
     * @param null $arg3
     * @param null $arg4
     * @return $this
     */
    public function filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {
        imagefilter($this->gdImage, $filtertype, $arg1, $arg2, $arg3, $arg4);

        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function flip($mode)
    {
        imageflip($this->gdImage, $mode);

        return $this;
    }

    /**
     * @param $x
     * @param $y
     * @param $color
     * @return $this
     */
    public function drawPixel($x, $y, $color)
    {
        imagesetpixel($this->gdImage, $x, $y, $color);

        return $this;
    }

    /**
     * @param $font
     * @param $x
     * @param $y
     * @param $c
     * @param $color
     * @return $this
     */
    public function drawChar($font, $x, $y, $c, $color)
    {
        imagechar($this->gdImage, $font, $x, $y, $c, $color);

        return $this;
    }

    /**
     * @param $src_image Canvas
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $src_w
     * @param $src_h
     * @param $opacity
     * @param bool $gray
     * @return $this
     */
    public function merge($src_image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity, $gray = false)
    { // todo optional params -> fully overlay
        if ($gray) {
            imagecopymergegray(
                $this->gdImage,
                $src_image->getGdImage(),
                $dst_x,
                $dst_y,
                $src_x,
                $src_y,
                $src_w,
                $src_h,
                $opacity
            );
        } else {
            imagecopymerge(
                $this->gdImage,
                $src_image->getGdImage(),
                $dst_x,
                $dst_y,
                $src_x,
                $src_y,
                $src_w,
                $src_h,
                $opacity
            );
        }

        return $this;
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
     * @param $src_image Canvas
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $dst_w
     * @param $dst_h
     * @param $src_w
     * @param $src_h
     * @return $this
     */
    public function copy($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        imagecopyresampled(
            $this->gdImage,
            $src_image->getGdImage(),
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            $dst_w,
            $dst_h,
            $src_w,
            $src_h
        );

        return $this;
    }

    /**
     * @param $src_image
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $dst_w
     * @param $dst_h
     * @param $src_w
     * @param $src_h
     * @return $this
     */
    public function copyResized($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        imagecopyresized(
            $this->gdImage,
            $src_image->getGdImage(),
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            $dst_w,
            $dst_h,
            $src_w,
            $src_h
        );

        return $this;
    }

    /**
     * @return mixed
     */
    public function colorWhite()
    {
        return $this->createColor(0xFF, 0xFF, 0xFF);
    }

    /**
     * @return mixed
     */
    public function colorBlack()
    {
        return $this->createColor(0x00, 0x00, 0x00);
    }

    /**
     * @return mixed
     */
    public function colorRed()
    {
        return $this->createColor(0xFF, 0x00, 0x00);
    }

    /**
     * @return mixed
     */
    public function colorGreen()
    {
        return $this->createColor(0x00, 0xFF, 0x00);
    }

    /**
     * @return mixed
     */
    public function colorBlue()
    {
        return $this->createColor(0x00, 0x00, 0xFF);
    }

    /**
     * @return mixed
     */
    public function colorYellow()
    {
        return $this->createColor(0xFF, 0xFF, 0x00);
    }

    /**
     * @return mixed
     */
    public function colorMagenta()
    {
        return $this->createColor(0xFF, 0x00, 0xFF);
    }

    /**
     * @return mixed
     */
    public function colorCyan()
    {
        return $this->createColor(0x00, 0xFF, 0xFF);
    }

    /**
     * Creates a Text-Box with the upper-left corner at position {@link $x}/{@link $y}.
     * Optionally it adds {@link $padding_x}, {@link $padding_y}, {@link $margin_x} and {@link $margin_y}.
     * Via the border parameters, a border can be added.
     * All measures ({@link $padding_x}, {@link $padding_y}, {@link $margin_x}, {@link $margin_y}, {@link $$border_line_width})
     * can also be a percentage or negative. E. g.  {@link $margin_x} = "50%" or {@link $margin_x} = "-10%".
     * The percentage will be relative to the width/height of the text.
     *
     *
     * @param $fontSize
     * @param $angle
     * @param $x
     * @param $y
     * @param $color
     * @param $fontfile
     * @param $text
     * @param bool $backgroundColor
     * @param int|String $padding_x
     * @param int|String $padding_y
     * @param int|String $margin_x
     * @param int|String $margin_y
     * @param bool|int|String $border_color
     * @param bool|int|String $border_line_width
     */
    public function flexibleTextBox(
        $fontSize,
        $angle,
        $x,
        $y,
        $color,
        $fontfile,
        $text,
        $backgroundColor = false,
        $padding_x = 0,
        $padding_y = 0,
        $margin_x = 0,
        $margin_y = 0,
        $border_color = false,
        $border_line_width = false
    ) {
        if (!$backgroundColor) {
            $backgroundColor = $this->colorTransparent();
        }

        $textMeasure = imagettfbbox($fontSize, $angle, $fontfile, $text);

        $text_width = abs($textMeasure[4] - $textMeasure[0]);
        $text_height = abs($textMeasure[5] - $textMeasure[1]);

        $this->convertPercentageToPixel($padding_x, $text_width);
        $this->convertPercentageToPixel($padding_y, $text_height);
        $this->convertPercentageToPixel($margin_x, $text_width);
        $this->convertPercentageToPixel($margin_y, $text_height);


        $box_x = $x + $margin_x;
        $box_y = $y + $margin_y;
        $text_x = $box_x + $padding_x;
        $text_y = $box_y + $padding_y;

        $box_width = $text_width + (2 * $padding_x);
        $box_height = $text_height + (2 * $padding_y);

        $this->drawRectangle($box_x, $box_y, $box_x + $box_width, $box_y + $box_height, $backgroundColor, true);

        if ($border_color && $border_line_width) {
            $this->convertPercentageToPixel($border_line_width, $text_width);

            $border_x = $box_x - $border_line_width;
            $border_y = $box_y - $border_line_width;

            $border_width = $box_width + (2 * $border_line_width);
            $border_height = $box_height + (2 * $border_line_width);

            imagesetthickness($this->gdImage, $border_line_width);
            $this->drawRectangle(
                $border_x,
                $border_y,
                $border_x + $border_width,
                $border_y + $border_height,
                $border_color
            );
            imagesetthickness($this->gdImage, 1);
        }

        $this->drawText($fontSize, $text_x, $text_y + $text_height, $color, $fontfile, $text, $angle);
    }

    protected function convertPercentageToPixel(&$percentage, $hundertPercent)
    {
        if (is_string($percentage) && strpos($percentage, '%') !== false) {
            $percentage = str_replace('%', '', $percentage);
            $percentage = doubleval($percentage);
            $percentage = ($hundertPercent / 100) * $percentage;
        }
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $color
     * @param bool $filled
     * @return $this
     */
    public function drawRectangle($x1, $y1, $x2, $y2, $color, $filled = false)
    {
        if ($filled) {
            imagefilledrectangle($this->gdImage, $x1, $y1, $x2, $y2, $color);
        } else {
            imagerectangle($this->gdImage, $x1, $y1, $x2, $y2, $color);
        }

        return $this;
    }

    /**
     * @param $fontSize
     * @param $angle
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $color
     * @param $fontfile
     * @param $text
     * @param int $alignment
     * @param bool $backgroundColor
     * @param int|String $padding_x
     * @param int|String $padding_y
     * @param int|String $margin_x
     * @param int|String $margin_y
     * @param bool|int|String $border_color
     * @param bool|int|String $border_line_width
     */
    public function fixedTextBox(
        $fontSize,
        $angle,
        $x1,
        $y1,
        $x2,
        $y2,
        $color,
        $fontfile,
        $text,
        $alignment = self::TEXT_ALIGNMENT_CENTER,
        $backgroundColor = false,
        $padding_x = 0,
        $padding_y = 0,
        $margin_x = 0,
        $margin_y = 0,
        $border_color = false,
        $border_line_width = false
    ) {
        if (!$backgroundColor) {
            $backgroundColor = $this->colorTransparent();
        }

        $width = abs($x2 - $x1);
        $height = abs($y2 - $y1);

        $this->convertPercentageToPixel($padding_x, $width);
        $this->convertPercentageToPixel($padding_y, $height);
        $this->convertPercentageToPixel($margin_x, $width);
        $this->convertPercentageToPixel($margin_y, $height);

        $box_width = $width - (2 * $margin_x);
        $box_height = $height - (2 * $margin_y);

        $box_x = $x1 + $margin_x;
        $box_y = $y1 + $margin_y;

        $textMeasure = imagettfbbox($fontSize, $angle, $fontfile, $text);

        $text_width = abs($textMeasure[4] - $textMeasure[0]);
        $text_height = abs($textMeasure[5] - $textMeasure[1]);

        $text_x = $box_x + $padding_x;
        $text_y = $box_y + $padding_y + ((($box_height - (2 * $padding_y)) / 2) - ($text_height / 2));

        switch ($alignment) {
            case self::TEXT_ALIGNMENT_LEFT:
                break;

            case self::TEXT_ALIGNMENT_CENTER:
                $text_x += ((($box_width - (2 * $padding_x)) / 2) - ($text_width / 2));
                break;

            case self::TEXT_ALIGNMENT_RIGHT:
                $text_x += ($box_width - (2 * $padding_x)) - $text_width;
                break;
        }

        $this->drawRectangle($box_x, $box_y, $box_x + $box_width, $box_y + $box_height, $backgroundColor, true);

        if ($border_color && $border_line_width) {
            $this->convertPercentageToPixel($border_line_width, $width);

            $border_x = $box_x - $border_line_width;
            $border_y = $box_y - $border_line_width;

            $border_width = $box_width + (2 * $border_line_width);
            $border_height = $box_height + (2 * $border_line_width);

            imagesetthickness($this->gdImage, $border_line_width);
            $this->drawRectangle(
                $border_x,
                $border_y,
                $border_x + $border_width,
                $border_y + $border_height,
                $border_color
            );
            imagesetthickness($this->gdImage, 1);
        }

        $this->drawText($fontSize, $text_x, $text_y + $text_height, $color, $fontfile, $text, $angle);
    }

    /**
     * @param $fontSize
     * @param $angle
     * @param $x
     * @param $y
     * @param $color
     * @param $fontfile
     * @param $text
     */
    public function drawText($fontSize, $x, $y, $color, $fontfile, $text, $angle = 0)
    {
        imagettftext($this->gdImage, $fontSize, $angle, $x, $y, $color, $fontfile, $text);
    }

    /**
     * @param $color
     * @param $thickness
     */
    protected function prepareDraw($color, $thickness)
    {

    }

    public function rotate($angle)
    {
        $this->gdImage = imagerotate($this->gdImage, $angle, $this->colorTransparent());
        $this->width = imagesx($this->gdImage);
        $this->height = imagesy($this->gdImage);
    }

    // todo text etc...
}
