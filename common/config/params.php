<?php

use common\components\Service;

$clientConfig = require __DIR__ . '/client-config.php';

return [
    'bsVersion'                     => '4.x',
    'user.passwordResetTokenExpire' => 3600,
    'bsDependencyEnabled'           => true,
    'baseUrl'                       => 'http://domain.local/projects/api/web',
    'hash.salt'                     => 'CoreService777', // never ever change this on production
    'hash.length'                   => 7, // and also this
    'hash.alphabet'                 => 'abcdehijklmnopqrstuvwxyz1234567890', // along with this
    'notificationSender.baseUrl'    => 'http://api-notif.pertaminalubricants.com/',
    'notificationSender.headers'    => ['X-App-key' => 'LMSAppKey', 'X-App-secret' => 'LMSAppSecret'],
    'fcm'                           => [
        'baseUrl'   => 'https://fcm.googleapis.com/fcm/',
        'serverKey' => 'AAAA7NCkm9Q:APA91bFSOo6Uvr9pIm1FXka7DIsWK5e9usa-Dw_QZbID2JVE8iPXwo5nGjOVZet68SrwaEhUYcaAw7jEKQgrKFeUBRbN8gbcEmvQKcttX5KOs4qjZgfDJ8hceXkYgQjq0xjD8g_oKLo2',
        'sender'    => 'NSM Notification Sender'
    ],
    'clientConfig'                  => $clientConfig,
];