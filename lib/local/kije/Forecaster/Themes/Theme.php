<?php


namespace kije\Forecaster\Themes;


use kije\Forecaster\Themes\Exception\ThemeException;

class Theme
{
    protected $themeURL;
    protected $themeData;
    protected $icons;

    public function __construct($themeURL)
    {
        $this->themeURL = $themeURL;
        $this->loadTheme();
    }

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

    public function getFont($pice = false)
    {
        if ($pice) {
            return $this->get('font.' . $pice);
        }
        return $this->get('font');
    }

    public function get($configPath)
    {
        $pices = explode('.', $configPath);

        $res = & $this->themeData;
        foreach ($pices as $pice) {
            if (array_key_exists($pice, $res)) {
                $res = & $res[$pice];
            } else {
                throw new ThemeException(
                    sprintf('Config path <pre>%s</pre> does not exist.', $configPath)
                );
            }
        }

        return $res;
    }

    public function getRegion($regionCode)
    {
        return $this->get('regions.' . $regionCode);
    }

    public function getRegions()
    {
        return $this->get('regions');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getIcons()
    {
        return $this->get('icons');
    }

    public function getDefaultIcon()
    {
        return $this->getIcon('default');
    }

    public function getIcon($name)
    {
        return $this->get('icons.' . $name);
    }

    public function getWeatherIcon($weatherCode)
    {
        return $this->getIcon('weather.' . $weatherCode);
    }

    public function getWindIcon()
    {
        return $this->getIcon('wind');
    }

    public function getPollenIcon($pollenCode)
    {
        return $this->getIcon('pollen.' . $pollenCode);
    }

    public function getMap()
    {
        return $this->get('map');
    }
}
