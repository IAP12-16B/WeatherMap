<?php


namespace kije\ImagIX;

/**
 * Class for manipulating images. Uses the GD library.
 * @package kije\ImagIX
 */
class ImagIX
{

    private $rootLayer;

    public function __construct()
    {
        $this->rootLayer = new Layer(new Canvas(1200, 1200));
        echo $this->rootLayer->render()->getPNG24();
    }
} 