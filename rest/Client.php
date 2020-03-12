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
     * @param array|null $bodyParams Allow to overcome HTTP GET request size limit with sending request
     * in GET body (non-standard).
     * @return mixed
     */
    public function get($url, $query = [], array $bodyParams = null)
    {
        return $this->json($this->http->get(
            $url,
            null !== $bodyParams ? ['json' => $bodyParams] : ['query' => $query]
        ));
    }

    /**
     * @param string $url
     * @param array|string $body
     * @param bool $isRawBody
     * @return mixed
     * @throws Exception
     */
    public function post($url, $body = [], bool $isRawBody = false)
    {
        return $this->json($this->http->post($url, $isRawBody ? ['body' => $body] : ['json' => $body]));
    }

    /**
     * @param string $url
     * @param array|string $body
     * @param bool $isRawBody
     * @return mixed
     * @throws Exception
     */
    public function put($url, $body = [], bool $isRawBody = false)
    {
        return $this->json($this->http->put($url, $isRawBody ? ['body' => $body] : ['json' => $body]));
    }

    /**
     * @param string $url
     * @param array|string $body
     * @param bool $isRawBody
     * @return mixed
     * @throws Exception
     */
    public function patch($url, $body = [], bool $isRawBody = false)
    {
        return $this->json($this->http->patch($url, $isRawBody ? ['body' => $body] : ['json' => $body]));
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
        if (
            $response->hasHeader('Content-Type')
            && false !== strpos($response->getHeader('Content-Type')[0], 'application/json')
        ) {
            $result = json_decode($result);
        }
        return $result;
    }
}
