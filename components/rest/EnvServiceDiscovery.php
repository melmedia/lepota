<?php

namespace lepota\components\rest;

use Exception;
use yii\base\Component;

/**
 * Load service locations from environment variables
 */
class EnvServiceDiscovery extends Component implements ServiceDiscoveryInterface
{
    /**
     * @param string $serviceName
     * @return string|null
     */
    public function getLocation(string $serviceName)
    {
        $location = getenv(strtoupper($serviceName) . '_SERVICE');
        return false !== $location ? $location : null;
    }
}
