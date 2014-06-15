<?php


namespace kije\ImagIX;


use kije\ImagIX\Exception\LayerException;

/**
 * Class Layer
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
 * @method textBox($size, $angle, $x, $y, $color, $fontfile, $text, $backgroundColor = false, $padding_x = 0, $padding_y = 0, $margin_x = 0, $margin_y = 0)
 *
 * @delegate Canvas
 */
class Layer
{

    /**
     * @var Layer[] $childLayers
     */
    private $childLayers;

    private $width;
    private $height;
    private $posX;
    private $posY;

    /**
     * @var Canvas image;
     */
    private $canvas;

    public function __construct($posx = 0, $posy = 0, $width = 1, $height = 1, &$image = null)
    {
        if ($image) {
            $this->canvas = $image;
        } else {
            $this->canvas = new Canvas($width, $height);
        }

        $this->width = ($width ? $width : $this->canvas->getWidth());
        $this->height = ($height ? $height : $this->canvas->getHeight());
        $this->posX = $posx;
        $this->posY = $posy;

        $this->childLayers = array();
    }

    public static function fromFile($file)
    {
        $img = Canvas::fromFile($file);
        return new self(0, 0, $img->getWidth(), $img->getHeight(), $img);
    }

    /**
     * @return Canvas
     */
    public function getCanvas()
    {
        return $this->canvas;
    }

    /**
     * @param Canvas $image
     */
    public function setCanvas($image)
    {
        $this->canvas = $image;
    }

    /**
     * @throws Exception\LayerException
     * @return Canvas Layer (with child layers) rendered as Canvas
     */
    public function render()
    {
        if (!$this->canvas) {
            throw new LayerException('No image set!');
        }

        $canvas = new Canvas($this->canvas->getWidth(), $this->canvas->getHeight());

        ksort($this->childLayers);

        foreach ($this->childLayers as $layer) {
            $posX = $layer->getPosX();
            $posY = $layer->getPosY();
            $width = $layer->getWidth();
            $height = $layer->getHeight();

            $canvas->copy(
                $layer->render(),
                $posX,
                $posY,
                0,
                0,
                $width,
                $height,
                $width,
                $height
            );
        }

        return $canvas;
    }

    /**
     * @return int
     */
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     * @param int $posX
     */
    public function setPosX($posX)
    {
        $this->posX = $posX;
    }

    /**
     * @return int
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     * @param int $posY
     */
    public function setPosY($posY)
    {
        $this->posY = $posY;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function addChildLayer($index, &$layer)
    {
        if (!array_key_exists($index, $this->childLayers)) {
            $this->childLayers[$index] = $layer;
        } else {
            throw new LayerException(
                'Child layer already exists at this index. Use replaceChildLayer() to replace it.'
            );
        }
    }

    public function replaceChildLayer($index, &$layer)
    {
        if (array_key_exists($index, $this->childLayers)) {
            $this->childLayers[$index] = $layer;
        } else {
            throw new LayerException('No child layer exists at this index. Use addChildLayer() to add it.');
        }
    }

    public function removeChildLayer($index)
    {
        if (array_key_exists($index, $this->childLayers)) {
            // reference save destroy ;)
            $this->childLayers[$index] = null;
            unset($this->childLayers[$index]);
        }
    }

    public function __call($method, $args)
    {
        // Delegate draw methods
        if (method_exists($this->canvas, $method)) {
            return call_user_func_array(array($this->canvas, $method), $args);
        }

        return false;
    }

    public function __destruct()
    {
        unset($this->canvas); // free
    }
}
