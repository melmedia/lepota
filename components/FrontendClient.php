<?php
namespace lepota\components;

use Exception;
use GuzzleHttp\RequestOptions;
use Yii;
use yii\base\Component;

class FrontendClient extends Component
{
    /** @var string Authorization Bearer token */
    public $token;

    /** integer */
    public $port = 3000;

    /** @var \lepota\rest\Client */
    protected $restClient;

    public function init()
    {
        $frontendIP = Yii::$app->request->userIP ?? '127.0.0.1';
        $this->restClient = new \lepota\rest\Client([
            // send request to frontend using client IP address for current request
            'base_uri' => "http://{$frontendIP}:{$this->port}/",
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
