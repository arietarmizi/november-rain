<?php

namespace api\forms\account;

use api\components\BaseForm;
use common\models\User;

class ChangePasswordForm extends BaseForm
{

    public $oldPassword;
    public $newPassword;
    public $confirmPassword;


    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'confirmPassword'], 'required'],
            [['oldPassword', 'newPassword', 'confirmPassword'], 'string', 'min' => 6, 'max' => 255],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword'],
            ['oldPassword', 'validateOldPassword']
        ];
    }

    public function validateOldPassword($attribute, $params)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!$user->validatePassword($this->oldPassword)) {
            $this->addError($attribute, \Yii::t('app', 'Invalid Password'));
        }
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['oldPassword', 'newPassword', 'confirmPassword']
        ];
    }

    public function submit()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $user->setPassword($this->newPassword);
        return $user->save();

    }

    /**
     * @return mixed
     *
     * How to format the data
     * @since 2018-05-06 23:08:33
     */
    public function response()
    {
        return [];
    }
}