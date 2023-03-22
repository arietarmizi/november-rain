<?php

namespace api\forms\auth;

use api\components\BaseForm;
use Carbon\Carbon;
use common\models\ForgotPassword;
use common\models\User;
use common\validators\PhoneNumberValidator;
use Yii;

class ResetPasswordForm extends BaseForm
{
    public $phoneNumber;
    public $code;
    public $newPassword;
    /** @var User */
    protected $_user;
    /** @var ForgotPassword */
    protected $_forgotPassword;

    public function rules()
    {
        return [
            [['phoneNumber', 'code', 'newPassword'], 'required'],
            ['code', 'string'],
            ['newPassword', 'string', 'min' => 6],
            ['phoneNumber', PhoneNumberValidator::class],
        ];
    }

    public function submit()
    {
        $this->_findUser();
        if ($this->_user === null) {
            return false;
        }

        $this->_findForgotPassword();
        if ($this->_forgotPassword === null) {
            return false;
        }

        // save forgot password as used
        $this->_forgotPassword->usedAt = Carbon::now()->format('Y-m-d H:i:s');
        $this->_forgotPassword->status = ForgotPassword::STATUS_USED;
        $this->_forgotPassword->save(false);

        // save user password
        $this->_user->setPassword($this->newPassword);
        $this->_user->save(false);

        return true;
    }

    protected function _findUser()
    {
        $user = User::findOne([
            'phoneNumber' => $this->phoneNumber,
            'status'      => User::STATUS_ACTIVE
        ]);

        if ($user === null) {
            $this->addError('phoneNumber', Yii::t('app', 'Member does not have authentication data. Please contact helpdesk.'));
        }

        $this->_user = $user;
    }

    protected function _findForgotPassword()
    {
        $forgotPassword = ForgotPassword::findOne([
            'userId' => $this->_user->id,
            'code'   => $this->code,
            'status' => ForgotPassword::STATUS_ACTIVE,
        ]);

        if ($forgotPassword == null) {
            $this->addError('code', Yii::t('app', 'One time password code is incorrect.'));

            return;
        }

        $recordCreatedTime = Carbon::createFromTimestamp(strtotime($forgotPassword->createdAt))->format('Y-m-d H:i:s');
        $elapsed           = Carbon::now()->diffInSeconds($recordCreatedTime, true);

        if ($elapsed > ForgotPassword::EXP_DURATION) {
            $this->addError('code', Yii::t('app', 'One time password code is expired.'));

            return;
        }

        $this->_forgotPassword = $forgotPassword;
    }

    public function response()
    {
        return [];
    }
}
