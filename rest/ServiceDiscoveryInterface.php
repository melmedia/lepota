<?php
namespace lepota\rest;

/**
 * Get location for service name
 */
interface ServiceDiscoveryInterface
{

    /**
     * @param string $serviceName
     * @return string|null
     */
    public function getLocation($serviceName);

}
