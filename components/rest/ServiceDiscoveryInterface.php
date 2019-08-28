<?php
namespace lepota\components\rest;

/**
 * Get location for service name
 */
interface ServiceDiscoveryInterface
{

    /**
     * @param string $serviceName
     * @return string|null
     */
    public function getLocation(string $serviceName);

}
