<?php

namespace kije\Forecaster\Maps;


use kije\ImagIX\Document;

class TemperatureMapIterator extends AbstractMapIterator
{
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Document Can return any type.
     */
    public function &current()
    {
        $map = new TemperatureMap($this->theme, $this->data[$this->key()], $this->key());
        $this->currentMapDocument = $map->render();

        return $this->currentMapDocument;
    }
}
