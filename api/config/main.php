<?php

use api\components\ErrorHandler;
use common\components\Service;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-api',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'api\controllers',
    'timeZone'            => 'Asia/Jakarta',
    'components'          => [
        'request'                     => [
            'enableCookieValidation' => false,
            'enableCsrfCookie'       => false,
            'enableCsrfValidation'   => false,
        ],
        'user'                        => [
            'identityClass'   => \api\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-api', 'httpOnly' => true],
            'loginUrl'        => ['auth/login'],
        ],
        'response'                    => [
            'format' => Response::FORMAT_JSON,
        ],
        'log'                         => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'except' => [
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:402',
                    ],
                ],
            ],
        ],
        'errorHandler'                => [
            'class' => ErrorHandler::class,
        ],
        'urlManager'                  => [
            'class'               => 'yii\web\UrlManager',
            'showScriptName'      => false,
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => false,
            'rules'               => require(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php'),
        ],
        Service::SERVICE_NOTIFICATION => [
            'class'   => Service::class,
            'host'    => $params['notificationSender.baseUrl'],
            'headers' => $params['notificationSender.headers'],
        ],
    ],
    'params'              => $params,
];
