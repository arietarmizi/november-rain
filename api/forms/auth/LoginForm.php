<?php

namespace api\forms\auth;

use api\components\BaseForm;
use Carbon\Carbon;
use common\models\Company;
use common\models\Device;
use common\models\User;
use nadzif\base\validators\PhoneNumberValidator;
use Yii;

/**
 * Class LoginForm
 *
 * @package api\forms\auth
 *
 * @property string $deviceIdentifier
 * @property string $deviceOsType
 * @property string $deviceOsVersion
 * @property string $devicePlayerId
 * @property string $deviceModel
 * @property string $deviceAppVersion
 * @property string $deviceLatitude
 * @property string $deviceLongitude
 * @property string $deviceLastIp
 */
class LoginForm extends BaseForm
{
    public $phoneNumber;
    public $password;
    public $firebaseToken;

    /** @var User */
    protected $_user;
    /** @var Device */
    protected $_device;

    protected $_deviceIdentifier;
    protected $_deviceOsType;
    protected $_deviceOsVersion;
    protected $_devicePlayerId;
    protected $_deviceModel;
    protected $_deviceAppVersion;
    protected $_deviceLatitude;
    protected $_deviceLongitude;

    private $allowedTimeLogin = 1;

    public function init()
    {
        parent::init();

        $this->_deviceIdentifier = Yii::$app->request->headers->get('X-Device-identifier', null);
        $this->_deviceOsType     = Yii::$app->request->headers->get('X-Device-osType', null);
        $this->_deviceOsVersion  = Yii::$app->request->headers->get('X-Device-osVersion', null);
        $this->_devicePlayerId   = Yii::$app->request->headers->get('X-Device-playerId', null);
        $this->_deviceModel      = Yii::$app->request->headers->get('X-Device-model', null);
        $this->_deviceAppVersion = Yii::$app->request->headers->get('X-Device-appVersion', null);
        $this->_deviceLatitude   = Yii::$app->request->headers->get('X-Device-latitude', null);
        $this->_deviceLongitude  = Yii::$app->request->headers->get('X-Device-longitude', null);
    }

    public function rules()
    {
        return [
            [
                [
                    'phoneNumber',
                    'password',
                    //                    'firebaseToken'
                ],
                'required'
            ],
            ['phoneNumber', PhoneNumberValidator::class],
            ['deviceIdentifier', 'validateByTime'],
            [
                [
                    'phoneNumber',
                    'password',
                    'firebaseToken',
                    'deviceOsType',
                    'deviceOsVersion',
                    'devicePlayerId',
                    'deviceModel',
                    'deviceAppVersion',
                    'deviceLatitude',
                    'deviceLongitude',
                    'deviceLastIp',
                ],
                'string'
            ]
        ];
    }

    public function validateByTime($attribute, $params)
    {
        /** @var Device $latestDevice */
        $latestDevice = Device::find()
            ->leftJoin(User::tableName(), User::tableName() . '.id = ' . Device::tableName() . '.userId')
            ->where([User::tableName() . '.phoneNumber' => $this->phoneNumber])
            ->orderBy(['createdAt' => SORT_DESC])
            ->one();

        if ($latestDevice
            && $latestDevice->createdAt > Carbon::now()->subMinute($this->allowedTimeLogin)->format('Y-m-d H:i:s')
        ) {
            $this->addError($attribute, \Yii::t('app', 'You allowed to login after {datetime}', [
                'datetime' => \Yii::$app
                    ->formatter
                    ->asDatetime(Carbon::createFromTimestamp(strtotime($latestDevice->createdAt))
                        ->addMinute($this->allowedTimeLogin)
                        ->format('Y-m-d H:i:s'))
            ]));
        }
    }

    /**
     * @return mixed
     */
    public function getDeviceIdentifier()
    {
        return $this->_deviceIdentifier;
    }

    public function getDeviceOsType()
    {
        return $this->_deviceOsType;
    }

    public function getDeviceOsVersion()
    {
        return $this->_deviceOsVersion;
    }

    public function getDevicePlayerId()
    {
        return $this->_devicePlayerId;
    }

    public function getDeviceModel()
    {
        return $this->_deviceModel;
    }

    public function getDeviceAppVersion()
    {
        return $this->_deviceAppVersion;
    }

    public function getDeviceLatitude()
    {
        return $this->_deviceLatitude;
    }

    public function getDeviceLongitude()
    {
        return $this->_deviceLongitude;
    }

    public function getDeviceLastIp()
    {
        return Yii::$app->request->getUserIP();
    }

    public function submit()
    {

        $this->_findUser();
        if ($this->_user === null) {
            return false;
        }

        Yii::beginProfile(LoginForm::class . '::validatePassword');
        $passwordCorrect = $this->_user->validatePassword($this->password);
        Yii::endProfile(LoginForm::class . '::validatePassword');

        if (!$passwordCorrect) {
            $this->addError('password', Yii::t('app', 'Incorrect password supplied'));
            return false;
        }
        $this->_createDevice();

        return true;

    }

    protected function _findUser()
    {
        /** @var User $user */
        $user = User::find()
            ->where(['phoneNumber' => $this->phoneNumber])
            ->with('company')->one();

        if ($user === null) {
            $this->addError('phoneNumber', Yii::t('app', 'Account not registered'));
        } else {
            if ($user->verified == User::ACCOUNT_VERIFIED_FALSE){
                $this->addError('verified', Yii::t('app', 'Your account is not verified, Please contact helpdesk.'));
            }elseif ($user->company->status == Company::STATUS_ACTIVE) {
                $this->_user = $user;
            }else {
                $this->addError('companyId', Yii::t('app', 'Company Currently Inactive, Please contact helpdesk.'));
            }
        }

    }

    protected function _createDevice()
    {
        $device                = new Device();
        $device->userId        = $this->_user->id;
        $device->firebaseToken = $this->firebaseToken;
        $device->osType        = $this->deviceOsType;
        $device->osVersion     = $this->deviceOsVersion;
        $device->identifier    = $this->deviceIdentifier;
        $device->playerId      = $this->devicePlayerId;
        $device->model         = $this->deviceModel;
        $device->appVersion    = $this->deviceAppVersion;
        $device->latitude      = $this->deviceLatitude;
        $device->longitude     = $this->deviceLongitude;
        $device->lastIp        = $this->deviceLastIp;
        $device->save();

        $device->refresh();

        $this->_device = $device;
    }

    function response()
    {
        $company      = $this->_user->company;
        $responseData = [
            'id'                 => $this->_user->id,
            'companyId'          => $company->id,
            'companyName'        => $company->name,
            'identityCardNumber' => $this->_user->identityCardNumber,
            'name'               => $this->_user->name,
            'type'               => $this->_user->type,
            'phoneNumber'        => $this->_user->phoneNumber,
            'email'              => $this->_user->email,
            'birthDate'          => $this->_user->birthDate,
            'status'             => $this->_user->status,
            'verified'           => $this->_user->verified,
        ];

        if ($this->_user->verified) {
            $device = $this->_device->toArray();

            unset($device['id']);
            unset($device['userId']);
            unset($device['status']);
            unset($device['createdAt']);
            unset($device['updatedAt']);

            $responseData['device'] = $device;
        }

        return $responseData;
    }
}