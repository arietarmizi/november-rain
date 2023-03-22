<?php

namespace api\forms\auth;

use api\components\BaseForm;
use common\models\loyalty\User;
use common\models\loyalty\UserVerification;
use Yii;

class ChangePhoneNumberForm extends BaseForm
{
    public $phoneNumber;

    public function rules()
    {
        return [
            ['phoneNumber', 'required'],
        ];
    }

    public function submit()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        // create user verification
        $verification = UserVerification::getNewUser($user->id);
        // TODO: send code to notification

        return true;
    }

    public function response()
    {
        return [];
    }
}
