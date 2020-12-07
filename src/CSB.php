<?php

namespace CSB;

use CSB\Contracts\TransportInterface;
use CSB\Exceptions\CSBException;
use CSB\Transport\AsyncTransport;
use CSB\Transport\CurlTransport;
use CSB\Transport\EmptyTransport;
use DateTime;

class CSB
{
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
     * @var string
     */
    protected $transport;
    
    /**
     * @var string
     */
    protected $accountID;
    
    /**
     * @var string
     */
    protected $userID;
    
    /**
     * Transport strategy.
     *
     * @var TransportInterface
     */
    protected $Transport;
    
    /**
     * Logger constructor.
     *
     * @param string $endpoint
     * @param string $apiKey
     * @param string $transport
     *
     * @throws CSBException
     */
    public function __construct($endpoint, $apiKey, $transport = 'sync')
    {
        $this->setEndpoint($endpoint);
        
        if ($apiKey != 'phpunit') {
            $this->setAPIKey($apiKey);
        } else {
            $transport = 'phpunit';
        }
        
        switch ($transport) {
            case 'async':
                return $this->Transport = new AsyncTransport($endpoint,
                                                             $apiKey);
            case 'phpunit':
                return $this->Transport = new EmptyTransport($endpoint,
                                                             $apiKey);
            default:
                return $this->Transport = new CurlTransport($endpoint, $apiKey);
        }
    }
    
    /**
     * Set CSB $endpoint.
     *
     * @param string $value
     *
     * @return CSB
     * @throws CSBException
     */
    public function setEndpoint($value)
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new CSBException('Invalid Endpoint');
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
     * @return CSB
     * @throws CSBException
     */
    public function setAPIKey($value)
    {
        $value = trim($value);
        
        if (empty($value)) {
            throw new CSBException('API key cannot be empty');
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
     * @return CSB
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        
        return $this;
    }
    
    /**
     * @param string $accountID
     * @param string $userID
     *
     * @return array
     * @throws CSBException
     */
    private function checkForAccountAndUserID($accountID, $userID)
    {
        if (empty($accountID)) {
            if (empty($this->accountID)) {
                throw new CSBException('Please Provide Account ID or Use Login Function');
            } else {
                $accountID = $this->accountID;
            }
        }
        
        if (empty($userID)) {
            if (empty($this->userID)) {
                throw new CSBException('Please Provide User ID or Use Login Function');
            } else {
                $userID = $this->userID;
            }
        }
        
        return [$accountID, $userID];
    }
    
    /**
     * @param $accountID
     * @param $userID
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function login($accountID, $userID)
    {
        list($accountID, $userID) = $this->checkForAccountAndUserID($accountID,
                                                                    $userID);
        
        $item = [
            'account_id' => $accountID,
            'user_id'    => $userID,
            'type'       => 'track',
            'event'      => 'User Login',
            'timestamp'  => date(DateTime::ISO8601),
        ];
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/login', $item);
    }
    
    /**
     * @param string|null $accountID
     * @param string|null $userID
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function logout($accountID = null, $userID = null)
    {
        list($accountID, $userID) = $this->checkForAccountAndUserID($accountID,
                                                                    $userID);
        
        $item = [
            'account_id' => $accountID,
            'user_id'    => $userID,
            'type'       => 'track',
            'event'      => 'User Logout',
            'timestamp'  => date(DateTime::ISO8601),
        ];
        
        $item = json_encode($item);
        
        $this->accountID = null;
        $this->userID    = null;
        
        return $this->Transport->send('/api/v1_1/logout', $item);
    }
    
    /**
     * @param string $accountID
     * @param array  $traits
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function account($accountID, $traits = [])
    {
        if (!empty($accountID)) {
            $this->accountID = $accountID;
        } else {
            throw new CSBException('Invalid Account ID');
        }
        
        $item = array_merge([
                                'account_id' => $accountID
                            ], $traits);
        
        $item = array_unique($item);
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/account', $item);
    }
    
    /**
     * @param string $accountID
     * @param string $userID
     * @param array  $traits
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function user($accountID, $userID, $traits = [])
    {
        list($accountID, $userID) = $this->checkForAccountAndUserID($accountID,
                                                                    $userID);
        
        $item = array_merge([
                                'account_id' => $accountID,
                                'user_id'    => $userID
                            ], $traits);
        
        $item = array_unique($item);
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/user', $item);
    }
    
    /**
     * @param string      $productID
     * @param string      $moduleID
     * @param string      $featureID
     * @param int         $total
     * @param string|null $accountID
     * @param string|null $userID
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function feature(
        $productID,
        $moduleID,
        $featureID,
        $total = 1,
        $accountID = null,
        $userID = null
    ) {
        list($accountID, $userID) = $this->checkForAccountAndUserID($accountID,
                                                                    $userID);
        
        if (empty($productID)) {
            throw new CSBException('Invalid Product ID');
        }
        
        if (empty($moduleID)) {
            throw new CSBException('Invalid Module ID');
        }
        
        if (empty($featureID)) {
            throw new CSBException('Invalid Feature ID');
        }
        
        $item = [
            'account_id' => $accountID,
            'user_id'    => $userID,
            'product_id' => $productID,
            'module_id'  => $moduleID,
            'feature_id' => $featureID,
            'total'      => $total,
            'type'       => 'feature',
            'timestamp'  => date(DateTime::ISO8601),
        ];
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/feature', $item);
    }
}
