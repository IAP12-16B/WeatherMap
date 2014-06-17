<?php


namespace kije\Forecaster\Maps;


use kije\Forecaster\Themes\Theme;
use kije\ImagIX\Document;
use kije\ImagIX\ImagIX;
use kije\ImagIX\Layer;

/**
 * Class AbstractMap
 * @package kije\Forecaster\Maps
 */
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

    /**
     * @param Theme $theme
     * @param array $data
     * @param int $date
     */
    public function __construct($theme, $data, $date)
    {
        $this->theme = $theme;
        $this->data = $data;
        $this->date = $date;
    }

    /**
     * Destroys the map. Needed to clean up memory.
     */
    public function destroy()
    {
        if ($this->doc) {
            $this->doc->destroy();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->render();
        return 'data:image/jpg;base64,' . base64_encode($this->doc->render()->getJPEG());
    }

    /**
     * Renders the map and returns a document
     * @return Document
     */
    public function render()
    {
        // check, if document already rendered
        if (!$this->doc) {

            // if not, craete a new document
            $this->doc = ImagIX::openDocument(
                $this->theme->getThemeURL() . '/' . $this->theme->getMap()
            );

            // if document creation was successful
            if ($this->doc) {

                // create a new layer
                $layer = new Layer(0, 0, $this->doc->getWidth(), $this->doc->getHeight());

                // add the layer to the document
                $this->doc->getRootLayer()->addChildLayer(10, $layer);

                // iterate over each region
                foreach ($this->theme->getRegions() as $code => $region) {

                    // draw a small dot ath the center of the region
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

                    // add the region name below the dot
                    $layer->flexibleTextBox(
                        18,
                        0,
                        $region['center']['x'],
                        $region['center']['y'],
                        $layer->colorBlack(),
                        $this->theme->getThemeURL() . '/' . $this->theme->getFont('localFile'),
                        $region['name'],
                        false,
                        0,
                        0,
                        "-50%",
                        15
                    );

                    // create an other layer for the region details. This layer is be 12% x 12% of the width of the map
                    $infoLayerSize = (($layer->getWidth() / 100) * 12);
                    $infoLayer_x = $region['center']['x'] - ($infoLayerSize / 2);
                    $infoLayer_y = $region['center']['y'] - ($infoLayerSize / 2);

                    $infoLayer = new Layer($infoLayer_x, $infoLayer_y, $infoLayerSize, $infoLayerSize);
                    $layer->addChildLayer($code, $infoLayer);

                    // call the abstract addInfos() method, which will be implemented in child classes
                    $this->addInfos($infoLayer, $region, $this->data[$code]);
                }

                // now, create a layer for the legend
                $legendLayer = new Layer(
                    $this->theme->get('legend.x'),
                    $this->theme->get('legend.y'),
                    $this->theme->get('legend.width'),
                    $this->theme->get('legend.height')
                );
                $layer->addChildLayer("legend", $legendLayer);

                // and call the abstract drawLegend() method, which will be implemented in child classes
                $this->drawLegend($legendLayer);

                // finally add the date in the lower right corner
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
     * Adds the info for a region. $layer is about 12% of the map size
     * @param Layer &$layer
     * @param array $region
     * @param array $data
     */
    protected abstract function addInfos(&$layer, $region, $data);

    /**
     * Draw the legend
     * @param $layer
     * @return mixed
     */
    protected abstract function drawLegend(&$layer);
}
