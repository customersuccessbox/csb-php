<?php

namespace CSB\Transport;

use CSB\Contracts\TransportInterface;

abstract class AbstractAPITransport implements TransportInterface
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * Custom url of the proxy if needed.
     *
     * @var string
     */
    protected $proxy;

    /**
     * AbstractApiTransport constructor.
     *
     * @param string $endpoint
     * @param string $apiKey
     */
    public function __construct($endpoint, $apiKey)
    {
        $this->endpoint = $endpoint;
        $this->apiKey   = $apiKey;
    }

    /**
     * Send a portion of the load to the remote service.
     *
     * @param string $uri
     * @param string $data
     *
     * @return void
     */
    abstract public function send($uri, $data);

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (empty($this->endpoint)
            && empty($this->apiKey)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getApiHeaders()
    {
        return [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }
}
