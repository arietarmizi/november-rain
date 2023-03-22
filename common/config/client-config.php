<?php

return [
    'lpgTracking' => [
        'X-Client-key'    => 'AppPlatformKey',
        'X-Client-secret' => 'AppPlatformSecret',
        'allowedIPs'      => [],
        'byPassError'     => false,
        'identities'      => [
            [
                'Identity'        => 'transporter@domain.com',
                'Id'              => '7b6c1ddd-65ef-4ee2-9159-0e6b9c3410ba',
                'AccessToken'     => '2afcfab07fa04650955112e53f069617.8b69791ddc394b00a5b413c56820b5b1',
                'FirebaseToken'   => 'firebaseToken.userTransporter',
                'Role'            => 'Transporter',
                'AllowedPlatform' => 'Mobile'
            ],
            [
                'Identity'        => 'outlet@domain.com',
                'Id'              => '583beb6c-ac4a-4f3a-9ef4-a39182913aca',
                'AccessToken'     => '1afcfab07fa04650955112e53f069617.8b69791ddc394b00a5b413c56820b5b0',
                'FirebaseToken'   => 'firebaseToken.userOutlet',
                'Role'            => 'Outlet',
                'AllowedPlatform' => 'Mobile'
            ],
        ]
    ]
];