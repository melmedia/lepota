<?php
namespace lepota\rest;

use Exception;

/**
 * Load service locations from JSON config file
 */
class JsonServiceDiscovery implements ServiceDiscoveryInterface
{
    protected $config;

    /**
     * @param string $configFile Full path to JSON config file
     * @throws Exception
     */
    public function __construct($configFile)
    {
        $this->config = json_decode(file_get_contents($configFile));
        if (!$this->config) {
            throw new Exception("Can't initialize configuration from JSON file $configFile");
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
