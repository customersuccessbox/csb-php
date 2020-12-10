## CSB PHP Package to Send Data to Server

#### Requirements

```shell script
php >= 5.4
```

#### Install Package

```composer log
composer require csb/php -vvv -no-dev --no-scripts --optimize-autoloader --ansi
```

#### Initiate Package

##### For Laravel, Add this Code to register function of AppServiceProvider.php

```php
$this->app->singleton('csb', function () {
    return new CSB(
        'https://{{domain}}.customersuccessbox.com',
        '{{secret}}',
        'async'
    );
});

$CSB = app('csb');
```

##### For PHP, Simply make singleton object, valid throughout request

```php
$CSB = new CSB(
    'https://{{domain}}.customersuccessbox.com',
    '{{secret}}',
    'async'
);
```

##### Note: We can use 'async' for Linux Systems only, For Windows Systems use 'sync'

#### Functions

##### Login

```php
$CSB->login('Account1', 'User1');
```

##### Logout

```php
$CSB->logout('Account1', 'User1');
```

##### Account [account($accountID, $properties = [])]

```php
$CSB->account('Account1', ['property1' => 'value1', 'property2' => 'value2', 'custom_Field' => 'custom_value']);
```

[Account Properties](https://developers.customersuccessbox.com/http-server-api/accounts)

##### User [user($accountID, $userID, $properties = [])]

```php
$CSB->user('Account1', 'User1', ['property1' => 'value1', 'property2' => 'value2', 'custom_Field' => 'custom_value']);
```

[User Properties](https://developers.customersuccessbox.com/http-server-api/users)

##### Subscription [subscription($accountID, $subscription, $properties = [])]

```php
$CSB->subscription('Account1', 'Subscription1', ['property1' => 'value1', 'property2' => 'value2']);
```

[Subscription Properties](https://developers.customersuccessbox.com/http-server-api/subscriptions)

##### Invoice [invoice($accountID = null, $subscriptionID = null, $invoiceID, $properties = [])]

```php
$CSB->invoice('Account1', null, 'Invoice1', ['property1' => 'value1', 'property2' => 'value2']);
$CSB->invoice(null, 'Subscription1', 'Invoice1', ['property1' => 'value1', 'property2' => 'value2']);
```

[Invoice Properties](https://developers.customersuccessbox.com/http-server-api/invoices)

##### Feature [feature($productID, $moduleID, $featureID, $total = 1, $accountID = null, $userID = null)]

###### To Send Features to CSB

```php
$CSB->feature('Account1', 'User1', 'ProductName', 'ModuleName', 'FeatureName');
```

[Feature Properties](https://developers.customersuccessbox.com/http-server-api/features)
