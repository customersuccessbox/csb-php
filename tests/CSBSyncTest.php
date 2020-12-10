<?php

use CSB\CSB;
use PHPUnit\Framework\TestCase;

class CSBSyncTest extends TestCase
{
    protected $CSB = null;
    
    public function setUp()
    {
        $this->CSB = $CSB = new CSB(
            'https://dbz.staging.customersuccessbox.com',
            'phpunit',
            'sync'
        );
        
        parent::setUp();
    }
    
    public function tearDown()
    {
        $this->CSB = null;
        
        parent::tearDown();
    }
    
    public function testLoginEvent()
    {
        try {
            $isSent = $this->CSB->login('Account1', 'User1');
            
            if ($isSent) {
                $this->assertTrue(true, 'Login Passed');
            } else {
                $this->assertTrue(false, 'Login Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Login Failed');
        }
    }
    
    public function testLogoutEvent()
    {
        try {
            $isSent = $this->CSB->logout('Account1', 'User1');
            
            if ($isSent) {
                $this->assertTrue(true, 'Logout Passed');
            } else {
                $this->assertTrue(false, 'Logout Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Logout Failed');
        }
    }
    
    public function testAccountEvent()
    {
        try {
            $isSent = $this->CSB->account('Account1', [
                'name' => 'Account1'
            ]);
            
            if ($isSent) {
                $this->assertTrue(true, 'Account Data Sent');
            } else {
                $this->assertTrue(false, 'Account Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Account Data Failed');
        }
    }
    
    public function testUserEvent()
    {
        try {
            $isSent = $this->CSB->user('Account1', 'User1', [
                'first_name' => 'FirstName 1',
                'last_name' => 'LastName 1'
            ]);
            
            if ($isSent) {
                $this->assertTrue(true, 'User Data Sent');
            } else {
                $this->assertTrue(false, 'User Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'User Data Failed');
        }
    }
    
    public function testSubscriptionEvent()
    {
        try {
            $isSent = $this->CSB->subscription('Account1', 'Subscription1', [
                'mrr' => 1000
            ]);
            
            if ($isSent) {
                $this->assertTrue(true, 'Subscription Data Sent');
            } else {
                $this->assertTrue(false, 'Subscription Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Subscription Data Failed');
        }
    }
    
    public function testInvoiceEvent()
    {
        try {
            $isSent = $this->CSB->invoice('Account1', null, 'Invoice1', [
                'status' => 'Paid'
            ]);
            
            if ($isSent) {
                $this->assertTrue(true, 'Invoice with Account Data Sent');
            } else {
                $this->assertTrue(false, 'Invoice with Account Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Invoice with Account Data Failed');
        }
        
        try {
            $isSent = $this->CSB->invoice(null, 'Subscription1', 'Invoice1', [
                'status' => 'Paid'
            ]);
            
            if ($isSent) {
                $this->assertTrue(true, 'Invoice with Subscription Data Sent');
            } else {
                $this->assertTrue(false,
                                  'Invoice with Subscription Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Invoice with Subscription Data Failed');
        }
    }
    
    public function testFeatureEvent()
    {
        try {
            $isSent = $this->CSB->feature(
                'Account1',
                'User1',
                'Product1',
                'Module1',
                'Feature1',
                20
            );
            
            if ($isSent) {
                $this->assertTrue(true, 'Feature Data Sent');
            } else {
                $this->assertTrue(false, 'Feature Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'Feature Data Failed');
        }
    }
}