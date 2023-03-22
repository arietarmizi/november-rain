<?php

namespace api\forms\inbox;

use api\components\BaseForm;
use common\models\User;
use common\models\UserInbox;

class UserInboxForm extends BaseForm
{

    public $messageId;
    public $status;

    protected $_deviceIdentifier;
    /** @var User */
    private $_user;
    private $_message;
    public  $_meta;

    public function rules()
    {
        return [
            [['status', 'messageId'], 'required'],
            [['messageId'], 'validateMessage'],
            [
                'status',
                'in',
                'range' => [UserInbox::STATUS_DELETED, UserInbox::STATUS_READ, UserInbox::STATUS_ARCHIVED]
            ],
        ];
    }

    public function validateMessage($attribute, $params = [])
    {
        $this->_user    = \Yii::$app->user->identity;
        $this->_message = UserInbox::find()
            ->where([
                'id'     => $this->messageId,
                'userId' => $this->_user->id
            ])
            ->one();

        if (!$this->_user || !$this->_message) {
            $this->addError($attribute, \Yii::t('app', 'User and Message not found'));
        }
    }

    public function submit()
    {
        UserInbox::updateAll(['status' => $this->status], ['id' => $this->_message->id]);
        return true;
    }

    public function response()
    {
        return [
            'id'     => $this->messageId,
            'status' => $this->status
        ];
    }

    public function meta()
    {
        return $this->_meta;
    }
}
