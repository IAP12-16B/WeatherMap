<?php

namespace kije\Forecaster;


use kije\Forecaster\CSV\Descriptor;
use kije\Forecaster\Maps\MapIterator;
use kije\Forecaster\Maps\PollenMapIterator;
use kije\Forecaster\Maps\TemperatureMapIterator;
use kije\Forecaster\Maps\WeatherMapIterator;
use kije\Forecaster\Maps\WindMapIterator;
use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Canvas;
use kije\ImagIX\Document;

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

        $this->readCSV();
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

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return PollenMapIterator
     */
    public function getPollenMaps()
    {
        return new PollenMapIterator($this->csvData, $this->theme);
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
            $maps[$date] = $this->saveMap($map, $date, self::CSV_NAME_POLLEN, $path, $relativePaths, $size);
        }

        return $maps;
    }

    protected function preparePath($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

    /**
     * @return WeatherMapIterator
     */
    public function getWeatherMaps()
    {
        return new WeatherMapIterator($this->csvData, $this->theme);
    }

    /**
     * @param Document $mapDoc
     * @param $date
     * @param $mapType
     * @param $path
     * @param $relativePaths
     * @param $size
     * @return mixed|string
     */
    protected function saveMap($mapDoc, $date, $mapType, $path, $relativePaths, $size)
    {
        $filename = sprintf('%s_%s.%s', $mapType, $date, 'jpg');
        $fullPath = $path . '/' . $filename;
        $canvas = $mapDoc->render();
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
            $maps[$date] = $this->saveMap($map, $date, self::CSV_NAME_TEMPERATURE, $path, $relativePaths, $size);
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
            $maps[$date] = $this->saveMap($map, $date, self::CSV_NAME_WEATHER, $path, $relativePaths, $size);
        }

        return $maps;
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
            $maps[$date] = $this->saveMap($map, $date, self::CSV_NAME_WIND, $path, $relativePaths, $size);
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
}
