<?php

namespace CSB\Transport;


use CSB\Configuration;
use CSB\Contracts\TransportInterface;
use CSB\Exceptions\CSBException;

abstract class AbstractAPITransport implements TransportInterface
{
    /**
     * @var string
     */
    const MAX_QUEUE_LENGTH = 100;

    /**
     * Key to authenticate remote calls.
     *
     * @var Configuration
     */
    protected $Configuration;

    /**
     * Custom url of the proxy if needed.
     *
     * @var string
     */
    protected $proxy;

    /**
     * Queue of messages to send.
     *
     * @var array
     */
    protected $queue = [];

    /**
     * AbstractApiTransport constructor.
     *
     * @param Configuration $Configuration
     *
     * @throws CSBException
     */
    public function __construct(Configuration $Configuration)
    {
        $this->Configuration = $Configuration;
        $this->verifyOptions($Configuration->getOptions());
    }

    /**
     * Verify if given options match constraints.
     *
     * @param $options
     *
     * @throws CSBException
     */
    protected function verifyOptions($options)
    {
        foreach ($this->getAllowedOptions() as $name => $regex) {
            if (isset($options[$name])) {
                $value = $options[$name];
                if (preg_match($regex, $value)) {
                    $this->$name = $value;
                } else {
                    throw new CSBException("Option '$name' has invalid value");
                }
            }
        }
    }

    /**
     * Add a message to the queue.
     *
     * @param array $item
     *
     * @return TransportInterface
     */
    public function addEntry($item): TransportInterface
    {
        if (count($this->queue) <= self::MAX_QUEUE_LENGTH) {
            $this->queue[] = $item;
        }

        return $this;
    }

    /**
     * Deliver everything on the queue to LOG Engine.
     *
     * @return void
     */
    public function flush()
    {
        if (empty($this->queue)) {
            return;
        }

        $this->send($this->queue);

        $this->queue = [];
    }

    /**
     * Send data chunks based on MAX_POST_LENGTH.
     *
     * @param array $items
     */
    public function send($items)
    {
        $json       = json_encode($items);
        $jsonLength = strlen($json);
        $count      = count($items);

        if ($jsonLength > $this->Configuration::MAX_POST_LENGTH) {
            if ($count === 1) {
                // It makes no sense to divide into chunks, just fail silently
                return;
            }
            $maxCount = floor($count / ceil($jsonLength / $this->Configuration::MAX_POST_LENGTH));
            $chunks   = array_chunk($items, $maxCount);
            foreach ($chunks as $chunk) {
                $this->send($chunk);
            }
        } else {
            $this->sendChunk($json);
        }
    }

    /**
     * Send a portion of the load to the remote service.
     *
     * @param string $data
     *
     * @return void
     */
    abstract protected function sendChunk($data);

    /**
     * List of available transport's options with validation regex.
     *
     * ['param-name' => 'regex']
     *
     * @return mixed
     */
    protected function getAllowedOptions()
    {
        return [
            'proxy' => '/.+/', // Custom url for
            'debug' => '/^(0|1)?$/',  // boolean
        ];
    }

    /**
     * @return array
     */
    protected function getApiHeaders()
    {
        return [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $this->Configuration->getApiKey(),
        ];
    }
}
