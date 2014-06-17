<?php


namespace kije\ImagIX;

    /**
     * Class Document
     * @package kije\ImagIX
     */
/**
 * Class Document
 * @package kije\ImagIX
 */
class Document
{
    private $width;
    private $height;
    private $rootLayer;

    /**
     * @param $width
     * @param $height
     * @param null $rootLayer
     */
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

    /**
     * @param $file
     * @return Document
     */
    public static function openFile($file)
    {
        $rLayer = Layer::fromFile($file);

        return new self($rLayer->getWidth(), $rLayer->getHeight(), $rLayer);
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return \kije\ImagIX\Layer
     */
    public function &getRootLayer()
    {
        return $this->rootLayer;
    }

    /**
     * @return Canvas
     */
    public function render()
    {
        return $this->rootLayer->render();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     *
     */
    public function destroy()
    {
        $this->rootLayer->destroy();
        unset($this->rootLayer); // free
    }
}
