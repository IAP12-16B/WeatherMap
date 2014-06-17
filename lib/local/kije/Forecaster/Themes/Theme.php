<?php


namespace kije\Forecaster\Themes;


use kije\Forecaster\Themes\Exception\ThemeException;

/**
 * Class to load, store and access a Forecaster theme (.ftheme)
 * @package kije\Forecaster\Themes
 */
class Theme
{
    protected $themeURL;
    protected $themeData;
    protected $icons;

    /**
     * @param $themeURL
     */
    public function __construct($themeURL)
    {
        $this->themeURL = $themeURL;
        $this->loadTheme();
    }

    /**
     * @throws Exception\ThemeException
     */
    protected function loadTheme()
    {
        $this->themeData = array();
        $this->icons = array();
        if (is_dir($this->themeURL) && pathinfo($this->themeURL, PATHINFO_EXTENSION) == 'ftheme') {
            $themeFileURL = $this->themeURL . '/theme.json';
            if (is_file($themeFileURL)) {
                $this->themeData = json_decode(file_get_contents($themeFileURL), true);
            } else {
                throw new ThemeException('Theme has no <pre>theme.json</pre> file!');
            }
        } else {
            throw new ThemeException(
                'Loading theme failed! No such directory or no <pre>.ftheme</pre> directory provided!'
            );
        }
    }

    /**
     * @return mixed
     */
    public function getThemeURL()
    {
        return $this->themeURL;
    }

    /**
     * @param bool $pice
     * @return mixed
     */
    public function getFont($pice = false)
    {
        if ($pice) {
            return $this->get('font.' . $pice);
        }
        return $this->get('font');
    }

    /**
     * @param $configPath
     * @return mixed
     * @throws Exception\ThemeException
     */
    public function get($configPath)
    {
        $pices = explode('.', $configPath);

        $res = & $this->themeData;
        foreach ($pices as $pice) {
            if ($res && array_key_exists($pice, $res)) {
                $res = & $res[$pice];
            } else {
                throw new ThemeException(
                    sprintf('Config path <pre>%s</pre> does not exist.', $configPath)
                );
            }
        }

        return $res;
    }

    /**
     * @param $regionCode
     * @return mixed
     */
    public function getRegion($regionCode)
    {
        return $this->get('regions.' . $regionCode);
    }

    /**
     * @return mixed
     */
    public function getRegions()
    {
        return $this->get('regions');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return mixed
     */
    public function getIcons()
    {
        return $this->get('icons');
    }

    /**
     * @return mixed
     */
    public function getDefaultIcon()
    {
        return $this->getIcon('default');
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getIcon($name)
    {
        return $this->get('icons.' . $name);
    }

    /**
     * @param $weatherCode
     * @return mixed
     */
    public function getWeatherIcon($weatherCode)
    {
        return $this->getIcon('weather.' . $weatherCode);
    }

    /**
     * @return mixed
     */
    public function getWindIcon()
    {
        return $this->getIcon('wind');
    }

    /**
     * @param $pollenCode
     * @return mixed
     */
    public function getPollenIcon($pollenCode)
    {
        return $this->getIcon('pollen.' . $pollenCode);
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return $this->get('map');
    }
}
