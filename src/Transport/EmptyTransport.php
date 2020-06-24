<?php

namespace CSB\Transport;

class EmptyTransport extends AbstractAPITransport
{
    /**
     * CurlTransport constructor.
     *
     * @param string $endpoint
     * @param string $apiKey
     *
     */
    public function __construct($endpoint, $apiKey)
    {
        parent::__construct($endpoint, $apiKey);
    }
    
    /**
     * Deliver items to LOG Engine.
     *
     * @param string $uri
     * @param string $data
     *
     * @return bool
     */
    public function send($uri, $data)
    {
        return true;
    }
}
