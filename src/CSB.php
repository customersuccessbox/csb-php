<?php


namespace CSB;


use CSB\Contracts\TransportInterface;
use CSB\Exceptions\CSBException;
use CSB\Transport\AsyncTransport;
use CSB\Transport\CurlTransport;
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
        switch ($transport) {
            case 'async':
                $this->Transport = new AsyncTransport($endpoint, $apiKey);
                break;
            default:
                $this->Transport = new CurlTransport($endpoint, $apiKey);
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
    public function setTransport(string $transport)
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

    private function track($event, $properties)
    {
        $item = [
            'accountId' => $properties['account_id'],
            'userId'    => $properties['user_id'],
            'type'      => 'track',
            'event'     => $event,
            'timestamp' => date(DateTime::ISO8601),
        ];

        $this->Transport->send('/api/v1_1/track', $item);
    }

    /**
     * @param $accountID
     * @param $userID
     *
     * @throws CSBException
     */
    public function login($accountID, $userID)
    {
        if (!empty($accountID)) {
            $this->accountID = $accountID;
        } else {
            throw new CSBException('Invalid Account ID');
        }

        if (!empty($userID)) {
            $this->userID = $userID;
        } else {
            throw new CSBException('Invalid User ID');
        }

        $this->track('User Login', [
            'account_id' => $accountID,
            'user_id'    => $userID
        ]);
    }

    /**
     * @param string $accountID
     * @param array  $traits
     *
     * @throws CSBException
     */
    private function account($accountID, $traits = [])
    {
        if (!empty($accountID)) {
            $this->accountID = $accountID;
        } else {
            throw new CSBException('Invalid Account ID');
        }

        $item = [
            'accountId' => $accountID,
            'type'      => 'account',
            'traits'    => $traits,
            'timestamp' => date(DateTime::ISO8601),
        ];

        $this->Transport->send('/api/v1_1/account', $item);
    }

    /**
     * @param string $accountID
     * @param string $userID
     * @param array  $traits
     *
     * @throws CSBException
     */
    private function user($accountID, $userID, $traits = [])
    {
        if (!empty($accountID)) {
            $this->accountID = $accountID;
        } else {
            throw new CSBException('Invalid Account ID');
        }

        if (!empty($userID)) {
            $this->userID = $userID;
        } else {
            throw new CSBException('Invalid User ID');
        }

        $item = [
            'accountId' => $accountID,
            'userId'    => $userID,
            'type'      => 'user',
            'traits'    => $traits,
            'timestamp' => date(DateTime::ISO8601),
        ];

        $this->Transport->send('/api/v1_1/identify', $item);
    }

    /**
     * @param string|null $accountID
     * @param string|null $userID
     *
     * @throws CSBException
     */
    public function logout($accountID = null, $userID = null)
    {
        [$accountID, $userID] = $this->checkForAccountAndUserID($accountID, $userID);

        $this->track('User Logout', [
            'account_id' => $accountID,
            'user_id'    => $userID
        ]);
    }

    /**
     * @param string      $productID
     * @param string      $moduleID
     * @param string      $featureID
     * @param int         $total
     * @param string|null $accountID
     * @param string|null $userID
     *
     * @throws CSBException
     */
    public function feature($productID, $moduleID, $featureID, $total = 1, $accountID = null, $userID = null)
    {
        [$accountID, $userID] = $this->checkForAccountAndUserID($accountID, $userID);

        $item = [
            'accountId' => $accountID,
            'userId'    => $userID,
            'productId' => $productID,
            'moduleId'  => $moduleID,
            'featureId' => $featureID,
            'total'     => $total,
            'type'      => 'feature',
            'timestamp' => date(DateTime::ISO8601),
        ];

        $this->Transport->send('/api/v1_1/feature', $item);
    }
}
