<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Document;
use kije\ImagIX\ImagIX;
use kije\ImagIX\Layer;

abstract class AbstractMap
{
    /**
     * @var Theme
     */
    protected $theme;
    protected $data;
    protected $date;
    /**
     * @var Document
     */
    private $doc;

    public function __construct($theme, $data, $date)
    {
        $this->theme = $theme;
        $this->data = $data;
        $this->date = $date;
    }

    /**
     * @return Document
     */
    public function render()
    {
        if (!$this->doc) {
            $this->doc = ImagIX::openDocument(
                $this->theme->getThemeURL() . '/' . $this->theme->getMap()
            );

            $layer = new Layer(0, 0, $this->doc->getWidth(), $this->doc->getHeight());

            $this->doc->getRootLayer()->addChildLayer(10, $layer);

            if ($this->doc) {
                foreach ($this->theme->getRegions() as $code => $region) {
                    $layer->drawArc(
                        $region['center']['x'],
                        $region['center']['y'],
                        10,
                        10,
                        0,
                        360,
                        $layer->colorBlack(),
                        true
                    );

                    $layer->flexibleTextBox(
                        18,
                        0,
                        $region['center']['x'],
                        $region['center']['y'],
                        $layer->colorBlack(),
                        $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                        $region['name'],
                        false, //$layer->colorWhite(),
                        0, //15,
                        0, //10,
                        "-50%", //"-60%",
                        15
                    /*,
                                        $layer->createColor(0xCC, 0xCC, 0xCC),
                                        1*/
                    );

                    $infoLayerSize = (($layer->getWidth() / 100) * 12);
                    $infoLayer_x = $region['center']['x'] - ($infoLayerSize / 2);
                    $infoLayer_y = $region['center']['y'] - ($infoLayerSize / 2);


                    $infoLayer = new Layer($infoLayer_x, $infoLayer_y, $infoLayerSize, $infoLayerSize);
                    $layer->addChildLayer($code, $infoLayer);
                    $this->addInfos($infoLayer, $region, $this->data[$code]);
                }

                $legendLayer = new Layer(
                    $this->theme->get('legend.x'),
                    $this->theme->get('legend.y'),
                    $this->theme->get('legend.width'),
                    $this->theme->get('legend.height')
                );
                $layer->addChildLayer("legend", $legendLayer);
                $this->drawLegend($legendLayer);

                $layer->drawText(
                    14,
                    $layer->getWidth() - 110,
                    $layer->getHeight() - 17,
                    $layer->colorBlack(),
                    $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                    date("d.m.Y", $this->date)
                );
            }
        }

        return $this->doc;
    }

    /**
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected abstract function addInfos(&$layer, $region, $data);

    protected abstract function drawLegend(&$layer);

    public function destroy()
    {
        if ($this->doc) {
            $this->doc->destroy();
        }
    }

    public function __toString()
    {
        $this->render();
        return 'data:image/jpg;base64,' . base64_encode($this->doc->render()->getJPEG());
    }
}
