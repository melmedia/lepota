<?php
namespace lepota\components\rest;

use Exception;
use Yii;
use yii\base\Component;

class ServiceWrapper extends Component
{
    /** @var string */
    public $serviceName;
    /** @var string */
    public $version;

    /** @var \lepota\rest\Client */
    protected $restClient;


    public function init()
    {
        $serviceUrl = Yii::$app->serviceDiscovery->getLocation($this->serviceName);
        if (!$serviceUrl) {
            throw new Exception("Can't find service location for $this->serviceName");
        }
        $this->restClient = new \lepota\rest\Client([
            'base_uri' => $this->version ? "$serviceUrl/v$this->version/" : $serviceUrl,
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
     * @param bool $isRawBody
     * @return mixed
     * @throws Exception
     */
    public function post($url, $body = [], bool $isRawBody = false)
    {
        return $this->restClient->post($url, $body, $isRawBody);
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
