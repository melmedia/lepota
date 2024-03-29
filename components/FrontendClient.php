<?php

namespace lepota\components;

use yii\base\Component;
use lepota\rest\Client;
use lepota\components\rest\EnvServiceDiscovery;
use GuzzleHttp\RequestOptions;

class FrontendClient extends Component
{
    /** @var string Authorization Bearer token */
    public $token;

    /** @var string */
    public $service = 'frontend';

    /** @var \lepota\rest\Client */
    protected $restClient;

    public function init()
    {
        $serviceDiscovery = new EnvServiceDiscovery();

        $this->restClient = new Client([
            'base_uri' => $serviceDiscovery->getLocation($this->service),
            RequestOptions::HEADERS => ['Authorization' => "Bearer {$this->token}"],
        ]);
    }

    /**
     * @param string $url
     * @param array $query
     * @return mixed
     */
    public function get($url, $query = [])
    {
        return $this->restClient->get($url, $query);
    }

    /**
     * @param string $url
     * @param array $body
     * @return mixed
     * @throws Exception
     */
    public function post($url, $body = [])
    {
        return $this->restClient->post($url, $body);
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function delete($url)
    {
        return $this->restClient->delete($url);
    }
}
