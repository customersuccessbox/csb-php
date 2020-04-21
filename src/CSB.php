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
     * @var string
     */
    protected $accountID;

    /**
     * @var string
     */
    protected $userID;

    /**
     * Agent configuration.
     *
     * @var Configuration
     */
    protected $Configuration;

    /**
     * Transport strategy.
     *
     * @var TransportInterface
     */
    protected $Transport;

    /**
     * Logger constructor.
     *
     * @param Configuration $Configuration
     *
     * @throws Exceptions\CSBException
     */
    public function __construct(Configuration $Configuration)
    {
        switch ($Configuration->getTransport()) {
            case 'async':
                $this->Transport = new AsyncTransport($Configuration);
                break;
            default:
                $this->Transport = new CurlTransport($Configuration);
        }

        $this->Configuration = $Configuration;
        register_shutdown_function([$this, 'flush']);
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

        $this->Transport->send($item);
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

        $this->Transport->send($item);
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

        $this->Transport->send($item);
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

        $this->Transport->send($item);
    }

    /**
     * Flush data to the remote platform.
     */
    public function flush()
    {
        if (!$this->Configuration->isEnabled()) {
            return;
        }

        $this->Transport->flush();
    }
}
