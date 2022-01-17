# Introduction

Bagisto WeAccept add-on allow customers to pay for others using WeAccept payment gateway.

## Requirements:

- **Bagisto**: 1.3.2

## Installation :
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

```
php artisan vendor:publish
```

-> Press 0 and then press enter to publish all assets and configurations.

> That's it, now just execute the project on your specified domain.
