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
     * @param $accountID
     * @param $userID
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function login($accountID, $userID)
    {
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        if (empty($userID)) {
            throw new CSBException('Invalid User ID');
        }
        
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
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        if (empty($userID)) {
            throw new CSBException('Invalid User ID');
        }
        
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
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        $item = array_merge([
                                'account_id' => $accountID
                            ], $traits);
        
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
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        if (empty($userID)) {
            throw new CSBException('Invalid User ID');
        }
        
        $item = array_merge([
                                'account_id' => $accountID,
                                'user_id'    => $userID
                            ], $traits);
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/user', $item);
    }
    
    /**
     * @param string $accountID
     * @param string $subscriptionID
     * @param array  $traits
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function subscription($accountID, $subscriptionID, $traits = [])
    {
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        if (empty($subscriptionID)) {
            throw new CSBException('Invalid User ID');
        }
        
        $item = array_merge([
                                'account_id'      => $accountID,
                                'subscription_id' => $subscriptionID
                            ], $traits);
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/subscription', $item);
    }
    
    /**
     * @param string|null $accountID
     * @param string|null $subscriptionID
     * @param string      $invoiceID
     * @param array       $traits
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function invoice(
        $accountID = null,
        $subscriptionID = null,
        $invoiceID = null,
        $traits = []
    ) {
        if (empty($accountID)) {
            if (empty($subscriptionID)) {
                throw new CSBException('Invalid Account ID or Subscription ID');
            }
        }
        
        if (empty($invoiceID)) {
            throw new CSBException('Invalid Invoice ID');
        }
        
        
        if (!empty($accountID)) {
            $item = array_merge([
                                    'account_id' => $accountID,
                                    'invoice_id' => $invoiceID
                                ], $traits);
        } else {
            $item = array_merge([
                                    'subscription_id' => $subscriptionID,
                                    'invoice_id'      => $invoiceID
                                ], $traits);
        }
        
        $item = json_encode($item);
        
        return $this->Transport->send('/api/v1_1/invoice', $item);
    }
    
    /**
     * @param string $accountID
     * @param string $userID
     * @param string $productID
     * @param string $moduleID
     * @param string $featureID
     * @param int    $total
     *
     * @return bool|TransportInterface
     * @throws CSBException
     */
    public function feature(
        $accountID,
        $userID,
        $productID,
        $moduleID,
        $featureID,
        $total = 1
    ) {
        if (empty($accountID)) {
            throw new CSBException('Invalid Account ID');
        }
        
        if (empty($userID)) {
            throw new CSBException('Invalid User ID');
        }
        
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
