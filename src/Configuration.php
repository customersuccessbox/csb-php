<?php

namespace CSB;


use InvalidArgumentException;

class Configuration
{
    /**
     * Max size of a POST request content.
     *
     * @var integer
     */
    const MAX_POST_LENGTH = 65536;
    
    /**
     * Remote endpoint to send data.
     *
     * @var string
     */
    protected $endpoint;
    
    /**
     * Authentication key.
     *
     * @var string
     */
    protected $apiKey;
    
    /**
     * @var bool
     */
    protected $enabled = true;
    
    /**
     * @var string
     */
    protected $transport = 'sync';
    
    /**
     * Transport options.
     *
     * @var array
     */
    protected $options = [];
    
    /**
     * Environment constructor.
     *
     * @param string $endpoint
     * @param string $apiKey
     *
     */
    public function __construct($endpoint, $apiKey = null)
    {
        if (is_string($endpoint) && is_string($apiKey)) {
            $this->setEndpoint($endpoint);
            $this->setAPIKey($apiKey);
        } else {
            $this->setEnabled(false);
        }
    }
    
    /**
     * Set CSB $endpoint.
     *
     * @param string $value
     *
     * @return Configuration
     */
    public function setEndpoint($value): Configuration
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new InvalidArgumentException('Invalid Endpoint');
        }
        
        $this->endpoint = $value;
        
        return $this;
    }
    
    /**
     * Get CSB endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    /**
     * Verify if api key is well formed.
     *
     * @param $value
     *
     * @return Configuration
     * @throws InvalidArgumentException
     */
    public function setAPIKey($value)
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new InvalidArgumentException('API key cannot be empty');
        }
        
        $this->apiKey = $value;
        
        return $this;
    }
    
    /**
     * Get current API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
    
    /**
     * Transport options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Add a new entry in the options list.
     *
     * @param string $key
     * @param        $value
     *
     * @return Configuration
     */
    public function addOption($key, $value): Configuration
    {
        $this->options[$key] = $value;
        
        return $this;
    }
    
    /**
     * Override the transport options.
     *
     * @param array $options
     *
     * @return Configuration
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        
        return $this;
    }
    
    /**
     * Check if data transfer is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return isset($this->apiKey) && is_string($this->apiKey) && $this->enabled;
    }
    
    /**
     * Able/Disable data transfer.
     *
     * @param bool $enabled
     *
     * @return Configuration
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
        
        return $this;
    }
    
    /**
     * Get current transport method.
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->transport;
    }
    
    /**
     * Set the preferred transport method.
     *
     * @param string $transport
     *
     * @return Configuration
     */
    public function setTransport(string $transport)
    {
        $this->transport = $transport;
        
        return $this;
    }
}
