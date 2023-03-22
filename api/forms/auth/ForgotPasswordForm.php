<?php

namespace api\forms\auth;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Company;
use common\models\ForgotPassword;
use common\models\User;
use common\validators\PhoneNumberValidator;
use Yii;

class ForgotPasswordForm extends BaseForm
{

    public    $phoneNumber;
    protected $_deviceIdentifier;

    /** @var User */
    protected $_user;
    protected $_forgotPassword;

    public function init()
    {
        parent::init();
        $this->_deviceIdentifier = Yii::$app->request->headers->get('X-Device-identifier', null);
    }

    public function rules()
    {
        return [
            [['phoneNumber', 'deviceIdentifier'], 'required'],
            ['phoneNumber', PhoneNumberValidator::class],
        ];
    }

    /**
     * @return mixed
     */
    public function getDeviceIdentifier()
    {
        return $this->_deviceIdentifier;
    }

    /**
     * @throws HttpException
     */
    public function submit()
    {
        $this->_findUser();
        if ($this->_user === null) {
            return false;
        }

        $this->_createForgotPassword();
        if ($this->_forgotPassword === null) {
            return false;
        }
        return true;
    }

    protected function _findUser()
    {

        $this->_user = User::find()
            ->where([User::tableName() . '.phoneNumber' => $this->phoneNumber])
            ->andWhere([User::tableName() . '.status' => User::STATUS_ACTIVE])
            ->with('company')->one();
        if ($this->_user === null) {
            $this->addError('phoneNumber', Yii::t('app', 'Account not registered'));
        } else {
            if ($this->_user->company->status !== Company::STATUS_ACTIVE) {
                throw new HttpException('400', Yii::t('app', 'Company Currently Inactive, Please contact helpdesk.'));
            }
            if ($this->_user->email === null) {
                throw new HttpException('400', Yii::t('app', 'Invalid email, Please contact helpdesk.'));
            }
        }
    }

    protected function _createForgotPassword()
    {
        if ($this->_user) {
            $forgotPassword             = new ForgotPassword();
            $forgotPassword->scenario   = ForgotPassword::SCENARIO_CREATE;
            $forgotPassword->userId     = $this->_user->id;
            $forgotPassword->identifier = $this->_deviceIdentifier;
            $forgotPassword->ip         = Yii::$app->request->getUserIP();

            if ($forgotPassword->validate() && $forgotPassword->save(false) && $forgotPassword->sendPasswordResetToken()) {
                $this->_forgotPassword = $forgotPassword;
            } else {
                $this->addErrors($forgotPassword->getErrors());
            }
        }
    }

    public function response()
    {
        return [
            'successMessage' => Yii::t('app', 'Request forgot password for account {phoneNumber} sent to {email}', [
                'phoneNumber' => $this->_user->phoneNumber,
                'email'       => $this->_user->email
            ])
        ];
    }

}
