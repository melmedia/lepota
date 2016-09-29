<?php
namespace lepota\rest;

use Exception;
use Psr\Http\Message\ResponseInterface;

class Client
{
    /** @var \GuzzleHttp\Client */
    protected $http;

    public function __construct(array $guzzleConfig = [])
    {
        $this->http = new \GuzzleHttp\Client($guzzleConfig);
    }

    /**
     * @param string $url
     * @param array $query
     * @return mixed
     */
    public function get($url, $query = [])
    {
        return $this->json($this->http->get($url, ['query' => $query]));
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
        $result = (string) $response->getBody();
        if ($response->hasHeader('Content-Type') && false !== strpos($response->getHeader('Content-Type')[0], 'application/json')) {
            $result = json_decode($result);
        }
        return $result;
    }

}
