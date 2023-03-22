<?php

namespace common\components;

use GuzzleHttp\Client;
use yii\base\Component;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

class Firebase extends Component
{
    const OPTION_TIMEOUT = 90.0;

    /** @var Client */
    private $_client;

    public function init()
    {
        $baseUrl   = ArrayHelper::getValue(\Yii::$app->params, 'fcm.baseUrl');
        $serverKey = ArrayHelper::getValue(\Yii::$app->params, 'fcm.serverKey');
        if (!$serverKey || !$baseUrl) {
            throw new Exception(\Yii::t('app', 'Invalid configurations'));
        }
        $client = [
            'base_uri' => $baseUrl,
            'timeout'  => self::OPTION_TIMEOUT,
            'headers'  => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json'
            ],
            'verify'   => false,
        ];

        $this->_client = new Client($client);
    }

    public function send($title, $body, $recipients)
    {
        $body   = [
            'title'             => $title,
            'body'              => $body,
            'badge'             => 1,
            'color'             => '#6f03fc',
            'content-available' => 1,
            'sound'             => 'default',
            'type_id'           => 1,
        ];
        $fields = [
            'registration_ids' => $recipients,
            'data'             => $body,
            'notification'     => $body
        ];

        try {
            $request = $this->_client->post('send', [
                'json' => $fields
            ]);
            return $request->getBody();
        } catch (ClientException $e) {
            return $e;
        }

    }
}