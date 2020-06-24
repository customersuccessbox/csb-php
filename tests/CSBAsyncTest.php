<?php

use CSB\CSB;
use PHPUnit\Framework\TestCase;

class CSBAsyncTest extends TestCase
{
    protected $CSB = null;
    
    public function setUp()
    {
        $this->CSB = $CSB = new CSB(
            'https://dbz.staging.customersuccessbox.com',
            'phpunit',
            'async'
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
            $isSent = $this->CSB->account('Account1', ['custom_Trait_1' => 123, 'custom_Trait_2' => 'Yes']);
            
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
            $isSent = $this->CSB->user('Account1', 'User1', ['custom_Trait_3' => 456, 'custom_Trait_4' => 'No']);
            
            if ($isSent) {
                $this->assertTrue(true, 'User Data Sent');
            } else {
                $this->assertTrue(false, 'User Data Failed');
            }
        } catch (Exception $exception) {
            $this->assertTrue(false, 'User Data Failed');
        }
    }
    
    public function testFeatureEvent()
    {
        try {
            $isSent = $this->CSB->feature(
                'Product1',
                'Module1',
                'Feature1',
                20,
                'Account1',
                'User1'
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