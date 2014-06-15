<?php


namespace kije\Forecaster\Maps;

use kije\Forecaster\Forecaster;
use kije\ImagIX\Document;
use kije\ImagIX\ImagIX;

/**
 * Class TemperatureMap
 * @package kije\Forecaster\Maps
 */
class TemperatureMap extends AbstractMap
{
    const DATA_NAME = Forecaster::CSV_NAME_TEMPERATURE;

    /**
     * @return Document
     */
    public function render()
    {
        $doc = ImagIX::openDocument(
            $this->theme->getThemeURL() . '/' . $this->theme->getMap()
        );

        if ($doc) {
            foreach ($this->theme->getRegions() as $code => $region) {
                $doc->drawArc($region['center']['x'], $region['center']['y'], 10, 10, 0, 360, $doc->colorBlack(), true);

                $doc->textBox(
                    18,
                    0,
                    $region['center']['x'],
                    $region['center']['y'],
                    $doc->colorBlack(),
                    $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                    $region['name'],
                    $doc->colorRed(),
                    5,
                    5
                );
            }
        }

        return $doc;
    }
}
