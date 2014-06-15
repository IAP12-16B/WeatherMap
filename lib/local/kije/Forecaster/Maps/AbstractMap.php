<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Document;

abstract class AbstractMap
{
    /**
     * @var Theme
     */
    protected $theme;
    protected $data;

    public function __construct($theme, $data)
    {
        $this->theme = $theme;
        $this->data = $data;
    }

    /**
     * @return Document
     */
    public abstract function render();
}
