<?php
namespace lepota\components\rest;

use Exception;
use GuzzleHttp\Client;
use yii\base\Component;
use Yii;

class ServiceWrapper extends Component
{
    /** @var string */
    public $serviceName;
    /** @var string */
    public $version;

    /** @var Client */
    protected $http;

    
    public function init()
    {
        $serviceUrl = Yii::$app->serviceDiscovery->getLocation($this->serviceName);
        if (!$serviceUrl) {
            throw new Exception("Can't find service location for $this->serviceName");
        }
        $this->http = new Client([
            'base_uri' => "http://$serviceUrl/$this->version/"
        ]);
    }

    /**
     * @param string $url
     * @param array $query
     * @return mixed
     */
    public function get($url, $query)
    {
        return json_decode($this->http->get($url, ['query' => $query])->getBody());
    }

    /**
     * @param string $url
     * @param array $body
     * @return mixed
     */
    public function post($url, $body)
    {
        return json_decode($this->http->post($url, ['json' => $body])->getBody());
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function delete($url)
    {
        return json_decode($this->http->delete($url)->getBody());
    }

}
