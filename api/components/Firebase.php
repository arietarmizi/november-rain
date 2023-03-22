<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 5/25/2018
 * Time: 1:02 AM
 */

namespace api\components;


use paragraph1\phpFCM\Recipient\Topic;
use understeam\fcm\Client;
use yii\base\Component;

class Firebase extends Component
{
    public $title;
    public $content;
    public $module;
    public $tokens     = false;
    public $lightColor = '#FF0000';
    public $data       = [];

    public function send()
    {
        /** @var Client $fcm */
        $fcm                         = \Yii::$app->fcm;
        $fcm->guzzleConfig['verify'] = false;

        $notification = $fcm->createNotification($this->title, $this->content);
        $notification->setIcon('notification_icon_resource_name')
            ->setColor($this->lightColor)
            ->setSound('default')
//            ->setClickAction('FCM_PLUGIN_ACTIVITY')
            ->setClickAction('FLUTTER_NOTIFICATION_CLICK')
            ->setBadge(1)
            ->setTitle($this->title)
            ->setBody($this->content);

        if ($this->tokens) {
            $message = $fcm->createMessage($this->tokens);
        } else {
            $message = $fcm->createMessage();
            $message->addRecipient(new Topic('all'));
        }

        $message->setTimeToLive(3600);
        $message->setNotification($notification)->setData($this->data);

        $response = $fcm->send($message);

        return $response->getStatusCode() == 200;

    }
}