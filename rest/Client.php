<?php
namespace lepota\rest;

use Exception;
use GuzzleHttp\Client;

class ServiceWrapper
{
    protected $http;

    /**
     * @param string $serviceName
     * @param string $version
     * @param ServiceDiscoveryInterface $serviceDiscovery object to request for service location
     * @throws Exception
     */
    public function __construct($serviceName, $version, ServiceDiscoveryInterface $serviceDiscovery)
    {
        $serviceUrl = $serviceDiscovery->getLocation($serviceName);
        if (!$serviceUrl) {
            throw new Exception("Can't find service location for $serviceName");
        }
        $this->http = new Client([
            'base_uri' => "http://$serviceUrl/$version/"
        ]);
    }

    /**
     * @param string $url
     * @param array $query
     * @return mixed
     */
    public function get($url, $query)
    {
        return json_decode($this->http->get($url, ['query' => $query])->getBody(), true);
    }

}
