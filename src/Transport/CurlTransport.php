<?php

namespace CSB\Transport;


use CSB\Configuration;
use CSB\Exceptions\CSBException;

class CurlTransport extends AbstractAPITransport
{
    /**
     * CurlTransport constructor.
     *
     * @param Configuration $configuration
     *
     * @throws CSBException
     */
    public function __construct($configuration)
    {
        // System need to have CURL available
        if (!function_exists('curl_init')) {
            throw new CSBException('cURL PHP extension is not available');
        }
        
        parent::__construct($configuration);
    }
    
    /**
     * Deliver items to LOG Engine.
     *
     * @param string $data
     */
    public function sendChunk($data)
    {
        $headers = [];
        
        foreach ($this->getApiHeaders() as $name => $value) {
            $headers[] = "$name: $value";
        }
        
        $handle = curl_init($this->Configuration->getEndpoint());
        
        curl_setopt($handle, CURLOPT_POST, 1);
        
        // Tell cURL that it should only spend 10 seconds trying to connect to the URL in question.
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        // A given cURL operation should only take 30 seconds max.
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);
        
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxy) {
            curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
        }
        curl_exec($handle);
        $errorNo = curl_errno($handle);
        $code    = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error   = curl_error($handle);
        
        if (0 !== $errorNo || 200 !== $code) {
            error_log(date('Y-m-d H:i:s') . " - [Warning] [" . get_class($this) . "] $error - $code $errorNo");
        }
        
        curl_close($handle);
    }
}