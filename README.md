## CSB PHP Package to Send Data to Server
#### Install Package
```composer log
composer require csb/php -vvv
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

#### Functions
##### Login
```php
$CSB->login('Account1', 'User1');
```
##### Logout
```php
$CSB->logout('Account1', 'User1');
```
##### Account [account($accountID, $traits = [])]
```php
$CSB->account('Account1', ['trait1' => 'value1', 'trait2' => 'value2', 'custom_Field' => 'custom_value']);
```
##### User [user($accountID, $userID, $traits = [])]
```php
$CSB->user('Account1', 'User1', ['trait1' => 'value1', 'trait2' => 'value2', 'custom_Field' => 'custom_value']);
```
##### Feature [feature($productID, $moduleID, $featureID, $total = 1, $accountID = null, $userID = null)]
```php
$CSB->feature('ProductName', 'ModuleName', 'FeatureName');
```
