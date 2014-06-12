<?php


namespace kije\ImagIX;


class Document
{
    private $width;
    private $height;

    private $rootLayer;

    public function __construct($width, $height, &$rootLayer = null) {
        if ($rootLayer) {
            $this->rootLayer = $rootLayer;
        } else {
            $this->rootLayer = new Layer(0, 0, $width, $height);
        }

        $this->width = $width;
        $this->height = $height;
    }

    public static function openFile($file) {
        $rLayer = Layer::fromFile($file);

        return new self($rLayer->getWidth(), $rLayer->getHeight(), $rLayer);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->rootLayer, $method), $args);
    }

    public function __destruct() {
        unset($this->rootLayer); // free
    }
} 