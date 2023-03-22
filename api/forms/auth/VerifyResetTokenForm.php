<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/24/2020
 * Time: 4:16 PM
 */

namespace api\forms\auth;


use api\components\BaseForm;
use api\components\HttpException;
use Carbon\Carbon;
use common\models\loyalty\ForgotPassword;
use common\models\loyalty\User;
use common\validators\PhoneNumberValidator;
use Yii;

class VerifyResetTokenForm extends BaseForm
{
    public $phoneNumber;
    public $verificationCode;

    /** @var User */
    private $_user;
    private $_validCode = false;

    public function rules()
    {
        return [
            [['phoneNumber', 'verificationCode'], 'required'],
            ['phoneNumber', PhoneNumberValidator::class],
            ['phoneNumber', 'validateUser'],
            ['verificationCode', 'string']
        ];
    }

    public function validateUser($attribute, $params = [])
    {
        $this->_user = User::find()
            ->where(['phoneNumber' => $this->phoneNumber, 'verified' => 1])
            ->one();


        if (!$this->_user) {
            $this->addError($attribute, Yii::t('app', 'User not registered or not verified'));
        }
    }

    /**
     * @return mixed
     *
     * What to do when submit. This will be called in the FormAction
     * @since 2018-05-06 23:08:20
     */
    public function submit()
    {
        /** @var ForgotPassword $forgotPassword */
        $forgotPassword = ForgotPassword::find()
            ->where([
                'userId' => $this->_user->id,
                'code'   => $this->verificationCode,
                'status' => User::STATUS_ACTIVE
            ])
            ->orderBy(['createdAt' => SORT_DESC])
            ->one();

        if (!$forgotPassword) {
            return false;
        }

        $recordCreatedTime = Carbon::createFromTimestamp(strtotime($forgotPassword->createdAt))->format('Y-m-d H:i:s');
        $elapsed           = Carbon::now()->diffInSeconds($recordCreatedTime, true);

        if ($elapsed > ForgotPassword::EXP_DURATION) {
            throw new HttpException(400, Yii::t('app', 'One time password code is expired.'));
        }

        $this->_validCode = true;
        return true;

    }

    /**
     * @return mixed
     *
     * How to format the data
     * @since 2018-05-06 23:08:33
     */
    public function response()
    {
        return [
            'phoneNumber'      => $this->phoneNumber,
            'verificationCode' => $this->verificationCode,
            'valid'            => $this->_validCode,
            'user'             => [
                'id'       => $this->_user->id,
                'outletId' => $this->_user->outletId,
                'name'     => $this->_user->name,
                'type'     => $this->_user->type,
            ]
        ];
    }
}