<?php
namespace lepota\components\rest;

use Exception;
use yii\base\Component;

/**
 * Load service locations from JSON config file
 */
class JsonServiceDiscovery extends Component implements ServiceDiscoveryInterface
{
    /** @var string $configFile Full path to JSON config file */
    public $configFile;

    protected $config;

    public function init()
    {
        $this->config = json_decode(file_get_contents($this->configFile), true);
        if (!$this->config) {
            throw new Exception("Can't initialize configuration from JSON file $this->configFile");
        }
    }

    /**
     * @param string $serviceName
     * @return string|null
     */
    public function getLocation($serviceName)
    {
        if (!isset($this->config[$serviceName])) {
            return null;
        }
        return $this->config[$serviceName];
    }
    
}
