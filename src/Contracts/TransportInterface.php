<?php

namespace CSB\Contracts;


interface TransportInterface
{
    /**
     * Add new log entry to the queue.
     *
     * @param string $uri
     * @param array  $data
     *
     * @return TransportInterface
     */
    public function send($uri, $data);
}
