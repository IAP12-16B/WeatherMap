<?php


namespace kije\Forecaster\CSV;


use kije\Forecaster\Forecaster;

class ForecasterDescriptor extends Descriptor
{

    function __construct(
        $separator = ',',
        $dateIndex = 0,
        $regionIndex = 1,
        $weatherIndex = 2,
        $temperatureIndex = 3,
        $windIndex = 4,
        $pollenIndex = 5
    ) {
        parent::__construct($separator);

        $this->addField(Forecaster::CSV_NAME_DATE, $dateIndex);
        $this->addField(Forecaster::CSV_NAME_REGION, $regionIndex);
        $this->addField(Forecaster::CSV_NAME_WEATHER, $weatherIndex);
        $this->addField(Forecaster::CSV_NAME_TEMPERATURE, $temperatureIndex);
        $this->addField(Forecaster::CSV_NAME_WIND, $windIndex);
        $this->addField(Forecaster::CSV_NAME_POLLEN, $pollenIndex);
    }
}
