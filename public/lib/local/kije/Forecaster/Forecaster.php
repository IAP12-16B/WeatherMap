<?php

namespace kije\Forecaster;


class Forecaster
{
    const CSV_NAME_DATE = "date";
    const CSV_NAME_REGION = "region";
    const CSV_NAME_WEATHER = "weather";
    const CSV_NAME_TEMPERATURE = "temperature";
    const CSV_NAME_WIND = "wind";
    const CSV_NAME_POLLEN = "pollen";

    private $csvDescriptor;
    private $csvURL;

    private $csvData;

    /**
     * @param CSV\Descriptor $csvDescriptor Descriptor instance, which describes the CSV.
     * @param String $csvURL Path/Url to CSV
     */
    public function __construct($csvDescriptor, $csvURL)
    {
        $this->csvDescriptor = $csvDescriptor;
        $this->csvURL = $csvURL;
    }


    private function readCSV() {
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
}