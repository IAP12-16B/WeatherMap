<?php

namespace kije\Forecaster;


use kije\ImagIX\ImagIX;

class Forecaster {

    private $imagIX;

    public function __construct() {
        $this->imagIX = new ImagIX();
    }
} 