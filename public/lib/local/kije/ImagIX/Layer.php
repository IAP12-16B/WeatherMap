<?php


namespace kije\ImagIX;


use kije\ImagIX\Exception\LayerException;

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
     * @var Image image;
     */
    private $image;

    public function __construct(&$image = null, $posx = 0, $posy = 0, $width = 1, $height = 1)
    {
        if ($image) {
            $this->image = $image;
        } else {
            $this->image = new Image($width, $height);
        }

        $this->width = ($width ? $width : $this->image->getWidth());
        $this->height = ($height ? $height : $this->image->getHeight());
        $this->posX = $posx;
        $this->posY = $posy;

        $this->childLayers = array();
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * @throws Exception\LayerException
     * @return Image Layer (with child layers) rendered as Image
     */
    public function render()
    {
        if (!$this->image) {
            throw new LayerException('No image set!');
        }

        $gdImage = $this->image->getGdImage();

        ksort($this->childLayers);

        foreach ($this->childLayers as $layer) {
            $posX = $layer->getPosX();
            $posY = $layer->getPosY();
            $width = $layer->getWidth();
            $height = $layer->getHeight();
            imagecopyresampled(
                $layer->render()->getGdImage(),
                $gdImage,
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

        $returnImage = new Image();
        $returnImage->setGdImage($gdImage);
        return $returnImage;
    }

    public function addChildLayer($index, &$layer)
    {
        if (!array_key_exists($index, $this->childLayers)) {
            $this->childLayers[$index] = $layer;
        } else {
            throw new LayerException('Child layer already exists at this index. Use replaceChildLayer() to replace it.');
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
} 