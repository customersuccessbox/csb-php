## CSB PHP Package to Send Data to Server
#### Requirements
```shell script
php >= 5.6
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
##### Account [account($accountID, $traits = [])]
```php
$CSB->account('Account1', ['trait1' => 'value1', 'trait2' => 'value2', 'custom_Field' => 'custom_value']);
```
[Account Traits](https://developers.customersuccessbox.com/http-server-api/accounts)
##### User [user($accountID, $userID, $traits = [])]
```php
$CSB->user('Account1', 'User1', ['trait1' => 'value1', 'trait2' => 'value2', 'custom_Field' => 'custom_value']);
```
[User Traits](https://developers.customersuccessbox.com/http-server-api/users)
##### Feature [feature($productID, $moduleID, $featureID, $total = 1, $accountID = null, $userID = null)]
###### To Send Features to CSB
```php
$CSB->feature('ProductName', 'ModuleName', 'FeatureName');
```
[Feature Traits](https://developers.customersuccessbox.com/http-server-api/features)
