<?php
namespace lepota\components\rest;

use Exception;
use yii\base\Component;

/**
 * Load service locations from JSON config file
 */
class JsonServiceDiscovery extends Component implements ServiceDiscoveryInterface
{
    /** @var string|string[] $configFile Full path to JSON config file */
    public $configFile;

    protected $config;

    public function init()
    {
        foreach ((array) $this->configFile as $configFile) {
            if (is_file($configFile)) {
                $this->config = json_decode(file_get_contents($configFile), true);
                break;
            }
        }
        if (!$this->config) {
            throw new Exception("Can't initialize configuration from JSON file");
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
