# Introduction

Bagisto WeAccept add-on allow customers to pay for others using WeAccept payment gateway.

## Requirements:

- **Bagisto**: 1.3.2

## Installation with composer:
- Run the following command
```
composer require bagisto/bagisto-weaccept-payment
```

- Goto config/concord.php file and add following line under 'modules'
```php
\Webkul\WeAccept\Providers\ModuleServiceProvider::class
```
- WeAccept Merchent Account's URL

    - Transaction response callback

    ```
    https://yourdomain.com/weaccept/paymob_txn_response_callback
    ```

- Run these commands below to complete the setup
```
composer dump-autoload
```

```
php artisan migrate
php artisan route:cache
php artisan optimize
```

-> Press 0 and then press enter to publish all assets and configurations.

## Installation without composer:

- Unzip the respective extension zip and then merge "packages" folder into project root directory.
- Goto config/app.php file and add following line under 'providers'.

```
Webkul\WeAccept\Providers\WeAcceptServiceProvider::class
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\WeAccept\\": "packages/Webkul/WeAccept/src"
```

- WeAccept Merchent Account's URL

    - Transaction response callback

    ```
    https://yourdomain.com/weaccept/paymob_txn_response_callback
    ```

- Run these commands below to complete the setup

```
composer dump-autoload
```
```
php artisan optimize
```
```
php artisan migrate
```

> That's it, now just execute the project on your specified domain.