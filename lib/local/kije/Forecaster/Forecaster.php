<?php

namespace kije\Forecaster;


use kije\Forecaster\CSV\Descriptor;
use kije\Forecaster\Maps\AbstractMap;
use kije\Forecaster\Maps\MapIterator;
use kije\Forecaster\Maps\PollenMap;
use kije\Forecaster\Maps\TemperatureMap;
use kije\Forecaster\Maps\WeatherMap;
use kije\Forecaster\Maps\WindMap;
use kije\Forecaster\Themes\Theme;

class Forecaster
{
    const CSV_NAME_DATE = "date";
    const CSV_NAME_REGION = "region";
    const CSV_NAME_WEATHER = "weather";
    const CSV_NAME_TEMPERATURE = "temperature";
    const CSV_NAME_WIND = "wind";
    const CSV_NAME_POLLEN = "pollen";

    /**
     * @var Descriptor
     */
    private $csvDescriptor;

    /**
     * @var String
     */
    private $csvURL;

    /**
     * @var array
     */
    private $csvData;

    /**
     * @var Theme
     */
    private $theme;

    /**
     * @param Descriptor $csvDescriptor Descriptor instance, which describes the CSV.
     * @param String $csvURL Path/Url to CSV
     * @param Theme $theme
     */
    public function __construct($csvDescriptor, $csvURL, $theme)
    {
        $this->csvDescriptor = $csvDescriptor;
        $this->csvURL = $csvURL;
        $this->theme = $theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    private function readCSV()
    {
        $this->csvData = array();
        if (file_exists($this->csvURL)) {
            $csvFileHandle = fopen($this->csvURL, "r");
            if ($csvFileHandle !== false) {
                while (($data = fgetcsv($csvFileHandle, 70, $this->csvDescriptor->getSeparator())) !== false) {
                    $key = strtotime($data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_DATE)]);
                    $regKey = $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_REGION)];
                    $this->csvData[$key][$regKey] = array(
                        self::CSV_NAME_DATE => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_DATE)],
                        self::CSV_NAME_REGION => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_REGION)],
                        self::CSV_NAME_WEATHER => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_WEATHER)],
                        self::CSV_NAME_TEMPERATURE => explode(
                            '/',
                            $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_TEMPERATURE)]
                        ),
                        self::CSV_NAME_WIND => explode(
                            '/',
                            $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_WIND)]
                        ),
                        self::CSV_NAME_POLLEN => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_POLLEN)]
                    );
                }
            }
        }
    }

    /**
     * @return AbstractMap[String][]
     */
    public function getMaps()
    {
        $this->readCSV();
        $maps = array(
            self::CSV_NAME_WEATHER => array(),
            self::CSV_NAME_TEMPERATURE => array(),
            self::CSV_NAME_WIND => array(),
            self::CSV_NAME_POLLEN => array()
        );
        foreach ($this->csvData as $date => $mapData) {
            $maps[self::CSV_NAME_WEATHER][] = new WeatherMap($this->theme, $mapData, $date);
            $maps[self::CSV_NAME_TEMPERATURE][] = new TemperatureMap($this->theme, $mapData, $date);
            $maps[self::CSV_NAME_WIND][] = new WindMap($this->theme, $mapData, $date);
            $maps[self::CSV_NAME_POLLEN][] = new PollenMap($this->theme, $mapData, $date);
        }


        return $maps;
    }
}