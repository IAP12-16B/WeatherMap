<?php


namespace kije\ImagIX;

/**
 * Class Document
 * @package kije\ImagIX
 * @method fill($color, $startX = 0, $startY = 0)
 * @method int colorTransparent()
 * @method int createColor($r, $g, $b, $a = false)
 * @method String getJPEG()
 * @method String toImage($filetype = Canvas::IMAGE_TYPE_JPEG, $filename = null, $quality = null, $filter = null)
 * @method saveJPEG($filename)
 * @method String getPNG()
 * @method savePNG($filename)
 * @method String getPNG24()
 * @method savePNG24($filename)
 * @method String getGIF()
 * @method saveGIF($filename)
 * @method crop($width, $height, $x = 0, $y = 0)
 * @method autocrop($mode = -1, $threshold = 0.5, $color = -1)
 * @method drawLine($x1, $y1, $x2, $y2, $color, $dashed = false)
 * @method drawArc($cx, $cy, $width, $height, $start, $end, $color, $filled)
 * @method drawEllipse($cx, $cy, $width, $height, $color)
 * @method drawPolygon($points, $num_points, $color)
 * @method drawRectangle($x1, $y1, $x2, $y2, $color)
 * @method filter($filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
 * @method flip($mode)
 * @method drawPixel($x, $y, $color)
 * @method drawChar($font, $x, $y, $c, $color)
 * @method merge($src_image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity, $gray = false)
 * @method resource getGdImage()
 * @method setGdImage($gd_image)
 * @method copy($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
 * @method copyResized($src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
 * @method int colorWhite()
 * @method int colorBlack()
 * @method int colorRed()
 * @method int colorGreen()
 * @method int colorBlue()
 * @method int colorYellow()
 * @method int colorMagenta()
 * @method int colorCyan()
 * @method Canvas getCanvas()
 * @method setCanvas($image)
 * @method Canvas render()
 * @method int getPosX()
 * @method int getPosY()
 * @method setPosX($posX)
 * @method setPosY($posY)
 * @method int getWidth()
 * @method int getHeight()
 * @method setWidth($width)
 * @method setHeight($height)
 * @method addChildLayer($index, &$layer)
 * @method replaceChildLayer($index, &$layer)
 * @method removeChildLayer($index)
 * @method textBox($size, $angle, $x, $y, $color, $fontfile, $text, $backgroundColor = false, $padding_x = 0, $padding_y = 0, $margin_x = 0, $margin_y = 0)
 *
 * @delegate Layer
 */
class Document
{
    private $width;
    private $height;

    private $rootLayer;

    public function __construct($width, $height, &$rootLayer = null)
    {
        if ($rootLayer) {
            $this->rootLayer = $rootLayer;
        } else {
            $this->rootLayer = new Layer(0, 0, $width, $height);
        }

        $this->width = $width;
        $this->height = $height;
    }

    public static function openFile($file)
    {
        $rLayer = Layer::fromFile($file);

        return new self($rLayer->getWidth(), $rLayer->getHeight(), $rLayer);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->rootLayer, $method), $args);
    }

    public function __destruct()
    {
        unset($this->rootLayer); // free
    }
}
