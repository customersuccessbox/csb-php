<?php

namespace CSB\Transport;

use CSB\Exceptions\CSBException;

/**
 * This transport collects log data until the end of processing.
 * It sends data executing shell curl and sending it to background (Asynchronous mode).
 */
class AsyncTransport extends AbstractAPITransport
{
    /**
     * CURL command path.
     *
     * @var string
     */
    protected $curlPath = 'curl';
    
    /**
     * AsyncTransport constructor.
     *
     * @param string $endpoint
     * @param string $apiKey
     *
     * @throws CSBException
     */
    public function __construct($endpoint, $apiKey)
    {
        if (!function_exists('exec')) {
            throw new CSBException("PHP function 'exec' is not available, is it disabled for security reasons?");
        }
        
        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            throw new CSBException('Exec transport is not supposed to work on Windows OS');
        }
        
        parent::__construct($endpoint, $apiKey);
    }
    
    /**
     * Send a portion of the load to the remote service.
     *
     * @param string $uri
     * @param string $data
     *
     * @return bool
     */
    public function send($uri, $data)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $cmd = "$this->curlPath -X POST";
        
        foreach ($this->getApiHeaders() as $name => $value) {
            $cmd .= " --header \"$name: $value\"";
        }
        
        $escapedData = $this->escapeArg($data);
        
        $cmd .= " --data '$escapedData' '" . $this->endpoint . $uri . "' --max-time 5";
        if ($this->proxy) {
            $cmd .= " --proxy '$this->proxy'";
        }
        
        // return immediately while curl will run in the background
        $cmd .= ' > /dev/null 2>&1 &';
        
        $output = [];
        exec($cmd, $output, $result);
        
        if ($result !== 0) {
            // curl returned some error
            error_log(date('Y-m-d H:i:s') . " - [Warning] [" . get_class($this) . "] $result ");
        }
        
        return true;
    }
    
    /**
     * Escape character to use in the CLI.
     *
     * @param $string
     *
     * @return mixed
     */
    protected function escapeArg($string)
    {
        return str_replace("'", "'\"'\"'", $string);
    }
}
