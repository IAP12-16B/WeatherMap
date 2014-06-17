<?php


namespace kije\Forecaster\CSV;


use kije\Forecaster\CSV\Exception\CSVDescriptorException;

/**
 * CSV descriptor class. Used to dynamically describe a CSV.
 * @package kije\Forecaster\CSV
 */
class Descriptor
{
    private $fields;
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->separator = $separator;
        $this->fields = array();
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @param $name
     * @param $index
     * @throws Exception\CSVDescriptorException
     */
    public function addField($name, $index)
    {
        if (!array_key_exists($name, $this->fields) && !in_array($index, $this->fields)) {
            $this->fields[$name] = $index;
        } else {
            throw new CSVDescriptorException('Field or index already defined');
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFieldByName($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
    }

    /**
     * @param $index
     * @return mixed
     */
    public function getFieldByIndex($index)
    {
        if (in_array($index, $this->fields)) {
            $arr = array_flip($this->fields);
            return $arr[$index];
        }
    }

    /**
     * @return array
     */
    public function  getFields()
    {
        return $this->fields;
    }
}
