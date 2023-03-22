<?php

namespace api\forms\auth;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Company;
use common\models\User;
use common\validators\PhoneNumberValidator;
use Yii;

/**
 * Class RegisterForm
 *
 * @package frontend\forms\auth
 *
 */
class RegisterForm extends BaseForm
{

    public $companyId;
    public $identityCardNumber;
    public $type;
    public $name;
    public $phoneNumber;
    public $email;
    public $birthDate;
    public $password;
    public $confirmPassword;

    /** @var User */
    protected $_user;
    protected $_companyId;

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        return [
            [
                [
                    'companyId',
                    'name',
                    'phoneNumber',
                    'email',
                    'birthDate',
                    'password',
                    'confirmPassword',
                ],
                'required'
            ],
            ['phoneNumber', 'trim'],
            [['email', 'phoneNumber'], 'unique', 'targetClass' => User::class],
            [['companyId', 'identityCardNumber'], 'string'],
            ['companyId', 'validateCompany'],
            ['name', 'string', 'min' => 6, 'max' => 255],
            ['phoneNumber', PhoneNumberValidator::class],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [['password', 'confirmPassword'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function validateCompany($attribute, $params)
    {
        $outlet = Company::findOne(['id' => $this->$attribute]);

        if ($outlet) {
            if ($outlet->status == Company::STATUS_ACTIVE) {
                $this->_companyId = $outlet->id;
            } else {
                $this->addError($attribute, Yii::t('app', 'Company Currently Inactive, Please contact helpdesk.'));
            }
        } else {
            $this->addError($attribute, Yii::t('app', 'Company code not belong to any Company'));
        }
    }

    /**
     * @return bool|mixed
     * @throws HttpException
     */
    public function submit()
    {
        return $this->_createUser();
    }

    protected function _createUser()
    {
        $transaction              = Yii::$app->db->beginTransaction();
        $user                     = new User();
        $user->companyId          = $this->_companyId;
        $user->identityCardNumber = $this->identityCardNumber;
        $user->name               = $this->name;
        $user->email              = $this->email;
        $user->phoneNumber        = $this->phoneNumber;
        $user->birthDate          = $this->birthDate;
        $user->verified           = false;
        $user->setPassword($this->password);
        if ($user->save()) {
            $user->refresh();
            $this->_user = $user;
            $transaction->commit();
            return true;
        } else {
            $this->addErrors($user->getErrors());
            $transaction->rollback();
            return false;
        }


    }

    function response()
    {
        return [
            'id'                 => $this->_user->id,
            'identityCardNumber' => $this->_user->identityCardNumber,
            'name'               => $this->_user->name,
            'type'               => $this->_user->type,
            'phoneNumber'        => $this->_user->phoneNumber,
            'email'              => $this->_user->email,
            'companyName'        => $this->_user->company->name,
            'birthDate'          => $this->_user->birthDate,
            'status'             => $this->_user->status,
            'verified'           => $this->_user->verified,
        ];
    }

}
