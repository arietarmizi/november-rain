<?php

namespace common\components;

use GuzzleHttp\Client;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Service
 *
 * @package common\components
 *
 * @property Client $client
 *
 */
class Service extends Component
{

    const SERVICE_NOTIFICATION = 'notificationService';
    const SERVICE_PRODUCTION   = 'qrCode';
    const OPTION_TIMEOUT       = 90.0;

    const HTTP_REQUEST_LOG_FILE = 'httpLogFile';
    const HTTP_REQUEST_LOG_DB   = 'httpLogDB';

    public $host;
    public $headers = [];

    /** @var Client */
    private $_client;

    public static function responseCodes()
    {
        return [
            self::SERVICE_PRODUCTION => [
                10001 => \Yii::t('app', 'Product validation success'),
                10002 => \Yii::t('app', 'Product validation failed'),
                30002 => \Yii::t('app', 'Code not registered'),
                30003 => \Yii::t('app', 'Code already claimed'),
                20001 => \Yii::t('app', 'Product claim success'),
                20002 => \Yii::t('app', 'Product claim success'),
            ],
        ];
    }

    public static function isOn($serviceName)
    {
        try {
            return ArrayHelper::getValue(\Yii::$app->params['serviceOn'], $serviceName, false);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function init()
    {
        $client = [
            'base_uri' => $this->host,
            'timeout'  => self::OPTION_TIMEOUT,
            'headers'  => $this->headers,
            'verify'   => false,
        ];

        $this->_client = new Client($client);
    }

    public function getClient()
    {
        return $this->_client;
    }

}
