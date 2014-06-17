<?php

namespace kije\Forecaster;


use kije\Forecaster\CSV\Descriptor;
use kije\Forecaster\Maps\AbstractMap;
use kije\Forecaster\Maps\MapIterator;
use kije\Forecaster\Maps\PollenMap;
use kije\Forecaster\Maps\PollenMapIterator;
use kije\Forecaster\Maps\TemperatureMap;
use kije\Forecaster\Maps\TemperatureMapIterator;
use kije\Forecaster\Maps\WeatherMap;
use kije\Forecaster\Maps\WeatherMapIterator;
use kije\Forecaster\Maps\WindMap;
use kije\Forecaster\Maps\WindMapIterator;
use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Canvas;

/**
 * The Forecaster its self. Reads CSV and delegated map generation
 * @package kije\Forecaster
 */
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
    public function __construct($csvDescriptor, $csvURL, $theme = null)
    {
        $this->csvDescriptor = $csvDescriptor;
        $this->csvURL = $csvURL;
        $this->theme = $theme;

        $this->readCSV();
    }

    /**
     *
     */
    private function readCSV()
    {
        $this->csvData = array();
        // this does not work with urls... even though the PHP doc says it should work...
        // check if the csv exists
        if (file_exists($this->csvURL)) {

            // open a read-only file handle to the file
            $csvFileHandle = fopen($this->csvURL, "r");

            if ($csvFileHandle !== false) {
                // read the csv line by line
                while (($data = fgetcsv($csvFileHandle, 70, $this->csvDescriptor->getSeparator())) !== false) {
                    // sort the read data by date
                    $key = strtotime($data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_DATE)]);

                    // and by region
                    $regKey = $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_REGION)];

                    // add the data to the array
                    $this->csvData[$key][$regKey] = array(
                        self::CSV_NAME_DATE => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_DATE)],
                        self::CSV_NAME_REGION => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_REGION)],
                        self::CSV_NAME_WEATHER => $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_WEATHER)],
                        // split min/max temperature
                        self::CSV_NAME_TEMPERATURE => explode(
                            '/',
                            $data[$this->csvDescriptor->getFieldByName(self::CSV_NAME_TEMPERATURE)]
                        ),
                        // split wind strength and direction
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
     * @param $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @param $path
     * @param bool $relativePaths
     * @param bool|array $size resize map before save
     * @return String[int]
     */
    public function savePollenMaps($path, $relativePaths = false, $size = false)
    {
        $this->preparePath($path);

        $maps = array();
        foreach ($this->getPollenMaps() as $date => $map) {
            $maps[$date] = $this->saveMap($map, $date, $path, self::CSV_NAME_POLLEN, $relativePaths, $size);
        }

        return $maps;
    }

    /**
     * @param $path
     */
    protected function preparePath($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

    /**
     * @return PollenMapIterator
     */
    public function getPollenMaps()
    {
        return new PollenMapIterator($this->csvData, $this->theme);
    }

    /**
     * @param AbstractMap $map
     * @param $date
     * @param $path
     * @param string $mapType
     * @param bool $relativePaths
     * @param bool $size
     * @return mixed|string
     */
    public function saveMap($map, $date, $path, $mapType = '', $relativePaths = false, $size = false)
    {
        $filename = sprintf('%s_%s.%s', $mapType, $date, 'jpg');
        $fullPath = $path . '/' . $filename;
        if (!file_exists($fullPath)) {
            $canvas = $map->render()->render();
            if (is_array($size)) {
                list($width, $height) = $size;

                if ($height == null) {
                    $height = $canvas->getHeight() * ($width / $canvas->getWidth());
                }
                $resizedCanvas = new Canvas($width, $height);

                $resizedCanvas->copyResized(
                    $canvas,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $canvas->getWidth(),
                    $canvas->getHeight()
                );
                $canvas = $resizedCanvas;
            }
            $canvas->saveJPEG($fullPath);
        }

        if ($relativePaths) {
            return str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPath);
        } else {
            return $fullPath;
        }
    }

    /**
     * @param $path
     * @param bool $relativePaths
     * @param bool|array $size resize map before save
     * @return String[int]
     */
    public function saveTemperatureMaps($path, $relativePaths = false, $size = false)
    {
        $this->preparePath($path);

        $maps = array();
        foreach ($this->getTemperatureMaps() as $date => $map) {
            $maps[$date] = $this->saveMap($map, $date, $path, self::CSV_NAME_TEMPERATURE, $relativePaths, $size);
        }

        return $maps;
    }

    /**
     * @return TemperatureMapIterator
     */
    public function getTemperatureMaps()
    {
        return new TemperatureMapIterator($this->csvData, $this->theme);
    }

    /**
     * @param $path
     * @param bool $relativePaths
     * @param bool|array $size resize map before save
     * @return String[int]
     */
    public function saveWeatherMaps($path, $relativePaths = false, $size = false)
    {
        $this->preparePath($path);

        $maps = array();
        foreach ($this->getWeatherMaps() as $date => $map) {
            $maps[$date] = $this->saveMap($map, $date, $path, self::CSV_NAME_WEATHER, $relativePaths, $size);
        }

        return $maps;
    }

    /**
     * @return WeatherMapIterator
     */
    public function getWeatherMaps()
    {
        return new WeatherMapIterator($this->csvData, $this->theme);
    }

    /**
     * @param $path
     * @param bool $relativePaths
     * @param bool|array $size resize map before save
     * @return String[int]
     */
    public function saveWindMaps($path, $relativePaths = false, $size = false)
    {
        $this->preparePath($path);

        $maps = array();
        foreach ($this->getWindMaps() as $date => $map) {
            $maps[$date] = $this->saveMap($map, $date, $path, self::CSV_NAME_WIND, $relativePaths, $size);
        }

        return $maps;
    }

    /**
     * @return WindMapIterator
     */
    public function getWindMaps()
    {
        return new WindMapIterator($this->csvData, $this->theme);
    }

    /**
     * Get a single map with the current theme.
     * @param int $date
     * @param String $type
     * @return PollenMap|TemperatureMap|WeatherMap|WindMap|null
     */
    public function getMap($date, $type)
    {
        // check if the date exists in the data array
        if (array_key_exists($date, $this->csvData)) {
            // if true, get the data
            $data = $this->csvData[$date];

            // and return the requested map
            switch ($type) {
                case self::CSV_NAME_POLLEN:
                    return new PollenMap($this->theme, $data, $date);
                    break;

                case self::CSV_NAME_TEMPERATURE:
                    return new TemperatureMap($this->theme, $data, $date);
                    break;

                case self::CSV_NAME_WIND:
                    return new WindMap($this->theme, $data, $date);
                    break;

                case self::CSV_NAME_WEATHER:
                    return new WeatherMap($this->theme, $data, $date);
                    break;
            }
        }
    }

    /**
     * Gets all dates, which are present in the data array
     * @param bool $timestamps
     * @param string $format
     * @return int[]
     */
    public function getDates($timestamps = true, $format = 'Y-m-d')
    {
        if ($timestamps) {
            return array_keys($this->csvData);
        } else {
            return array_map(
                function ($date) use ($format) {
                    return date($format, $date);
                },
                array_keys($this->csvData)
            );
        }

    }

    /**
     * Get the available map types. $key = displayable name; $value = type identifier
     * @return array
     */
    public function getTypes()
    {
        return array(
            'Wetter' => self::CSV_NAME_WEATHER,
            'Temperatur' => self::CSV_NAME_TEMPERATURE,
            'Wind' => self::CSV_NAME_WIND,
            'Pollen' => self::CSV_NAME_POLLEN
        );
    }

}
