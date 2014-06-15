<?php


namespace kije\Forecaster\Maps;

use kije\Forecaster\Forecaster;
use kije\ImagIX\Canvas;
use kije\ImagIX\Layer;

/**
 * Class TemperatureMap
 * @package kije\Forecaster\Maps
 */
class TemperatureMap extends AbstractMap
{
    const DATA_NAME = Forecaster::CSV_NAME_TEMPERATURE;


    /**
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected function addInfos(&$layer, $region, $data)
    {
        $regionTemp = $data[self::DATA_NAME];
        $min = $regionTemp[0];
        $max = $regionTemp[1];

        $layer->fixedTextBox(
            17,
            0,
            0,
            60,
            $layer->getWidth() / 2,
            95,
            ($max >= 0 ? $layer->colorBlack() : $layer->colorBlue()),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            $max . "° C",
            Canvas::TEXT_ALIGNMENT_CENTER,
            $layer->createColor(0xFF, 0xCC, 0xCC),
            15,
            10,
            5,
            0,
            $layer->createColor(0xCC, 0xCC, 0xCC),
            1
        );

        $layer->fixedTextBox(
            17,
            0,
            $layer->getWidth() / 2,
            60,
            $layer->getWidth(),
            95,
            ($min >= 0 ? $layer->colorBlack() : $layer->colorBlue()),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            $min . "° C",
            Canvas::TEXT_ALIGNMENT_CENTER,
            $layer->createColor(0xCC, 0xCC, 0xFF),
            15,
            10,
            5,
            0,
            $layer->createColor(0xCC, 0xCC, 0xCC),
            1
        );
    }

    /**
     * @param Layer $layer
     */
    protected function drawLegend(&$layer)
    {
        $layer->drawText(
            20,
            10,
            25,
            $layer->colorBlack(),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            "Temperaturen"
        );

        $layer->fixedTextBox(
            17,
            0,
            10,
            50,
            100,
            85,
            $layer->colorBlack(),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            "max.",
            Canvas::TEXT_ALIGNMENT_CENTER,
            $layer->createColor(0xFF, 0xCC, 0xCC),
            15,
            10,
            5,
            0,
            $layer->createColor(0xCC, 0xCC, 0xCC),
            1
        );

        $layer->fixedTextBox(
            17,
            0,
            10,
            95,
            100,
            125,
            $layer->colorBlack(),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            "min.",
            Canvas::TEXT_ALIGNMENT_CENTER,
            $layer->createColor(0xCC, 0xCC, 0xFF),
            15,
            10,
            5,
            0,
            $layer->createColor(0xCC, 0xCC, 0xCC),
            1
        );
    }
}
