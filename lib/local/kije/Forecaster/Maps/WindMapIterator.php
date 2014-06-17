<?php

namespace kije\Forecaster\Maps;


class WindMapIterator extends AbstractMapIterator
{
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return WindMap Can return any type.
     */
    public function &current()
    {
        $this->currentMap = new WindMap($this->theme, $this->data[$this->key()], $this->key());

        return $this->currentMap;
    }
}
