<?php
/**
 * Created by PhpStorm.
 * User: kije
 * Date: 6/16/14
 * Time: 8:07 PM
 */

namespace kije\Forecaster\Maps;


use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Document;

abstract class AbstractMapIterator implements \Iterator
{

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->currentMap) {
            $this->currentMap->destroy();
        }
        $this->position++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return int scalar on success, or null on failure.
     */
    public function key()
    {
        $keys = array_keys($this->data);
        return $keys[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $keys = array_keys($this->data);
        return array_key_exists($this->position, $keys);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if ($this->currentMap) {
            $this->currentMap->destroy();
        }
        $this->position = 0;
    }

    /**
     * @var String[String][int]
     */
    protected $data;
    protected $position;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var Document
     */
    protected $currentMap;

    function __construct($data, $theme)
    {
        $this->data = $data;
        $this->position = 0;
        $this->theme = $theme;
    }
}
