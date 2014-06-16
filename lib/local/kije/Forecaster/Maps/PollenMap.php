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
            "Pollenbelastung"
        );

        $pollen = array(
            'keine' => null,
            'schwach' => $this->theme->getPollenIcon(1),
            'mässig' => $this->theme->getPollenIcon(2),
            'stark' => $this->theme->getPollenIcon(3)
        );


        $y = 50;
        foreach ($pollen as $description => $iconName) {

            $layer->fixedTextBox(
                18,
                0,
                1,
                $y,
                190,
                $y + 40,
                $layer->colorBlack(),
                $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                $description,
                Canvas::TEXT_ALIGNMENT_LEFT,
                $layer->colorWhite(),
                65,
                10,
                5,
                0,
                $layer->createColor(0xCC, 0xCC, 0xCC),
                1
            );

            if ($iconName) {
                $iconCanvas = Canvas::fromFile($this->theme->getThemeURL() . '/' . $iconName);

                $iconWidth = 35;
                $iconHeight = $iconCanvas->getHeight() / ($iconCanvas->getWidth() / $iconWidth);

                $layer->copy(
                    $iconCanvas,
                    15,
                    $y + (20 - ($iconHeight / 2)),
                    0,
                    0,
                    $iconWidth,
                    $iconHeight,
                    $iconCanvas->getWidth(),
                    $iconCanvas->getHeight()
                );
            }

            $y += 50;
        }
    }
}
