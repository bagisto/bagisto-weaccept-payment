<?php

return [
    [
        'key' => 'sales',
        'name' => 'Sales',
        'sort' => 1
    ], [
        'key' => 'sales.paymentmethods',
        'name' => 'Payment Methods',
        'sort' => 2,
    ], [
        'key' => 'sales.paymentmethods.weaccept',
        'name' => 'WeAccept Payment',
        'sort' => 5,
        'fields' => [
            [
                'name' => 'title',
                'title' => 'weaccept::app.configuration.title',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'weaccept::app.configuration.description',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'active',
                'title' => 'weaccept::app.configuration.status',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Active',
                        'value' => true
                    ], [
                        'title' => 'Inactive',
                        'value' => false
                    ]
                ],
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'api_key',
                'title' => 'weaccept::app.configuration.api_key',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ], [
                'name' => 'merchant_id',
                'title' => 'weaccept::app.configuration.merchant_id',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ],[
                'name' => 'iframe_id',
                'title' => 'weaccept::app.configuration.iframe_id',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ],[
                'name' => 'integration_id',
                'title' => 'weaccept::app.configuration.integration_id',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ],[
                'name' => 'hmac_secret',
                'title' => 'weaccept::app.configuration.hmac_secret',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => false
            ], [
                'name' => 'sort',
                'title' => 'admin::app.admin.system.sort_order',
                'type' => 'select',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1
                    ], [
                        'title' => '2',
                        'value' => 2
                    ], [
                        'title' => '3',
                        'value' => 3
                    ], [
                        'title' => '4',
                        'value' => 4
                    ], [
                        'title' => '5',
                        'value' => 5
                    ]
                ],
            ]
        ]
    ]
];
