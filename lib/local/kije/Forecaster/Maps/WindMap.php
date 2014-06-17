<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Forecaster;
use kije\ImagIX\Canvas;
use kije\ImagIX\Layer;

class WindMap extends AbstractMap
{
    const DATA_NAME = Forecaster::CSV_NAME_WIND;
    private $dir2deg = array(
        'N' => 0,
        'NO' => 45,
        'O' => 90,
        'SO' => 135,
        'S' => 180,
        'SW' => 225,
        'W' => 270,
        'NW' => 315
    );

    /**
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected function addInfos(&$layer, $region, $data)
    {
        $windDirectory = $data[self::DATA_NAME][0];
        $windStrength = $data[self::DATA_NAME][1];

        // if not no wind
        if ($windDirectory != 'NN') {

            $icon = $this->theme->getThemeURL() . '/' . $this->theme->getWindIcon();

            $iconCanvas = Canvas::fromFile($icon);

            // rotate the icon
            $iconCanvas->rotate($this->directionToDegree($windDirectory));

            $iconWidth = $iconCanvas->getWidth() * 0.28;
            $iconHeight = $iconCanvas->getHeight() / ($iconCanvas->getWidth() / $iconWidth);

            // add it onto the map
            $layer->copy(
                $iconCanvas,
                (($layer->getWidth() / 2) - ($iconWidth / 2)),
                $layer->getHeight() / 4,
                0,
                0,
                $iconWidth,
                $iconHeight,
                $iconCanvas->getWidth(),
                $iconCanvas->getHeight()
            );
        }

        // add the wind strength text
        $layer->fixedTextBox(
            17,
            0,
            $layer->getWidth() / 7,
            (($layer->getHeight() / 7) * 4.9),
            ($layer->getWidth() / 7) * 6,
            (($layer->getHeight() / 7) * 6.1),
            $layer->colorBlack(),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            $windStrength . " km/h",
            Canvas::TEXT_ALIGNMENT_CENTER,
            $layer->colorWhite(),
            15,
            10,
            5,
            0,
            $layer->createColor(0xCC, 0xCC, 0xCC),
            1
        );
    }

    /**
     * Converts a direction (e.g. NW) to a angle
     * @param $direction
     * @return mixed
     */
    protected function directionToDegree($direction)
    {
        return $this->dir2deg[strtoupper($direction)];
    }

    /**
     * @param Layer $layer
     * @return mixed|void
     */
    protected function drawLegend(&$layer)
    {
        $layer->drawText(
            20,
            10,
            25,
            $layer->colorBlack(),
            $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
            "Wind"
        );

        $startY = 50;

        $icon = $this->theme->getThemeURL() . '/' . $this->theme->getWindIcon();

        foreach ($this->dir2deg as $dir => $deg) {
            $iconCanvas = Canvas::fromFile($icon);
            $iconCanvas->rotate($deg);
            $iconCanvas->autocrop(IMG_CROP_TRANSPARENT);

            $iconWidth = $iconCanvas->getWidth() * 0.18;
            $iconHeight = $iconCanvas->getHeight() / ($iconCanvas->getWidth() / $iconWidth);


            $layer->fixedTextBox(
                18,
                0,
                1,
                $startY,
                130,
                $startY + 30,
                $layer->colorBlack(),
                $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                $dir,
                Canvas::TEXT_ALIGNMENT_LEFT,
                $layer->colorWhite(),
                55,
                10,
                5,
                0,
                $layer->createColor(0xCC, 0xCC, 0xCC),
                1
            );

            $layer->copy(
                $iconCanvas,
                15,
                $startY + ((30 / 2) - ($iconHeight / 2)),
                0,
                0,
                $iconWidth,
                $iconHeight,
                $iconCanvas->getWidth(),
                $iconCanvas->getHeight()
            );


            $startY += 40;
        }
    }
}
