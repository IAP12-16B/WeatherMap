<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Forecaster;
use kije\ImagIX\Canvas;
use kije\ImagIX\Layer;

class WeatherMap extends AbstractMap
{
    const DATA_NAME = Forecaster::CSV_NAME_WEATHER;

    /**
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected function addInfos(&$layer, $region, $data)
    {
        $weather = $data[self::DATA_NAME];
        $icon = $this->theme->getThemeURL() . '/' . $this->theme->getWeatherIcon($weather);

        $iconCanvas = Canvas::fromFile($icon);

        $iconWidth = 110;
        $iconHeight = $iconCanvas->getHeight() / ($iconCanvas->getWidth() / $iconWidth);

        $layer->copy(
            $iconCanvas,
            (($layer->getWidth() / 2) - ($iconWidth / 2)),
            0,
            0,
            0,
            $iconWidth,
            $iconHeight,
            $iconCanvas->getWidth(),
            $iconCanvas->getHeight()
        );
    }

    protected function drawLegend(&$layer)
    {
        // TODO: Implement drawLegend() method.
    }
}
