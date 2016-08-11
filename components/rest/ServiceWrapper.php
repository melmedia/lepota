<?php
namespace lepota\components\rest;

use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Yii;
use yii\base\Component;

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
            'base_uri' => $this->version ? "$serviceUrl/v$this->version/" : $serviceUrl
        ]);
    }

    /**
     * @param string $url
     * @param array $query
     * @return mixed
     */
    public function get($url, $query = [])
    {
        $query = http_build_query($query);
        return json_decode(file_get_contents($url . ($query ? '?' . $query : '')));
    }

    /**
     * @param string $url
     * @param array $body
     * @return mixed
     * @throws Exception
     */
    public function post($url, $body = [])
    {
        return $this->json($this->http->post($url, ['json' => $body]));
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function delete($url)
    {
        return $this->json($this->http->delete($url));
    }

    protected function json(ResponseInterface $response)
    {
        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw new Exception("Status code is not good " . $response->getStatusCode());
        }
        return json_decode($response->getBody());
    }

}
