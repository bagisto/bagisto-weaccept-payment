<?php

return [
    'configuration' => [
        'title' => 'Title',
        'description' => 'Description',
        'status' => 'Status',
        'api_key' => 'API Key',
        'merchant_id' => 'Merchant Id',
        'iframe_id' => 'Iframe Id',
        'integration_id' => 'Encryption Id',
        'hmac_secret' => 'HMAC Secret'
    ],

    'error' => [
        'failure-message' => 'Your Order could not be completed due to mismatch token or verification. Please try again.',
        '1'  => 'Inactive Merchant ID.',
        '2'  => 'Inactive Payment Account.',
        '3'  => 'Insufficient funds.',
        '4'  => 'Incorrect Payment Account details.',
        '5'  => 'Invalid account.',
        '6' => 'The password of the Payment Account has expired.',
        'general' => 'WeAccept payment has been canceled.'
    ],

    'admin' => [
        'system' => [
                'weaccept_refund' => 'WeAccept Refund',
        ],
    
    ],
];
