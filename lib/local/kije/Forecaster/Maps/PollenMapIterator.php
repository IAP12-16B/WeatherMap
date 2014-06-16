<?php
/**
 * Created by PhpStorm.
 * User: kije
 * Date: 6/16/14
 * Time: 8:05 PM
 */

namespace kije\Forecaster\Maps;


use kije\ImagIX\Document;

/**
 * Class PollenMapIterator
 * @package kije\Forecaster\Maps
 */
class PollenMapIterator extends AbstractMapIterator
{

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Document Can return any type.
     */
    public function &current()
    {
        $map = new PollenMap($this->theme, $this->data[$this->key()], $this->key());
        $this->currentMapDocument = $map->render();

        return $this->currentMapDocument;
    }
}
