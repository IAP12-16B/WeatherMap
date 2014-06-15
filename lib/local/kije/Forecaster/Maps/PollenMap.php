<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Forecaster;
use kije\ImagIX\Canvas;
use kije\ImagIX\Layer;

class PollenMap extends AbstractMap
{
    const DATA_NAME = Forecaster::CSV_NAME_POLLEN;

    /**
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected function addInfos(&$layer, $region, $data)
    {
        $pollen = $data[self::DATA_NAME];
        if ($iconName = $this->theme->getPollenIcon($pollen)) {
            $icon = $this->theme->getThemeURL() . '/' . $iconName;

            $iconCanvas = Canvas::fromFile($icon);

            $iconWidth = 85;
            $iconHeight = $iconCanvas->getHeight() / ($iconCanvas->getWidth() / $iconWidth);

            $layer->copy(
                $iconCanvas,
                (($layer->getWidth() / 2) - ($iconWidth / 2)),
                20,
                0,
                0,
                $iconWidth,
                $iconHeight,
                $iconCanvas->getWidth(),
                $iconCanvas->getHeight()
            );
        }

    }

    protected function drawLegend(&$layer)
    {
        // TODO: Implement drawLegend() method.
    }
}
