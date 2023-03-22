<?php

namespace api\controllers\lpgtracking;

use common\actions\MockupFormAction;
use api\components\HttpException;
use common\helpers\Project;
use common\helpers\Randomizer;

class AuthController extends Controller
{
    public $hasJson = [
        'login',
        'forgot-password',
        'reset-password',
        'forgot-password-verification',
        'forgot-password-resend',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return $behaviors;
    }

    public function actions()
    {
        return [
            'login'                        => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['Identity', 'Password'], 'required'],
                    [['Identity'], 'email'],
                    [['Password'], 'string', 'min' => 8, 'max' => 32],
                ],
                'customValidation' => function () {
                    if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
                        throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 10010102);
                    }

                    $identity = Project::getIdentity($this->appKey, \Yii::$app->request->post('Identity'));
                    $password = 'password';

                    if (!$identity || \Yii::$app->request->post('Password') != $password) {
                        throw new HttpException(400, 'Incorrect identity or password supplied.', [], [], 10010101);
                    }
                },
                'successName'      => 'Login Success',
                'successMessage'   => 'Login Success',
                'successCode'      => 11010100,
                'successData'      => [
                    'User'   => [
                        'Id'          => 'CHECKIDENTITY:Id',
                        'Name'        => Randomizer::name(),
                        'Identity'    => "POST:Identity",
                        'PhoneNumber' => Randomizer::phoneNumber(),
                        'Address'     => Randomizer::address(),
                        'IsVerified'  => rand(0, 1),
                    ],
                    'Device' => [
                        'AccessToken'   => 'CHECKIDENTITY:AccessToken',
                        'ExpiredAt'     => Randomizer::futureDateTime(),
                        'FirebaseToken' => "POST:FirebaseToken",
                        'TimeZone'      => Randomizer::valueIn(['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura']),
                        'Language'      => 'id'
                    ],
                    'Role'   => [
                        'Name'            => "CHECKIDENTITY:Role",
                        'AllowedPlatform' => "CHECKIDENTITY:AllowedPlatform",
                    ],
                ],
                'referencedKeys'   => [
                    'User.Id',
                    'User.Identity',
                    'Device.AccessToken',
                    'Device.FirebaseToken',
                    'Role.Name',
                    'Role.AllowedPlatform',
                ]
            ],
            'forgot-password'              => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['Identity'], 'required'],
                    [['Identity'], 'email'],
                ],
                'customValidation' => function () {
                    $identity = Project::getIdentity($this->appKey, \Yii::$app->request->post('Identity'));

                    if (!$identity) {
                        throw new HttpException(400, 'Identity not registered', [], [], 10010401);
                    }
                },
                'successName'      => 'Forgot Password Success',
                'successMessage'   => 'OTP Code was sent to your email.',
                'successCode'      => 11010400,
                'successData'      => [
                    'Identity'      => "POST:Identity",
                    'ExpiredAt'     => Randomizer::futureDateTime(30, 30),
                    'AllowResendAt' => Randomizer::futureDateTime(5, 5),
                ],
                'referencedKeys'   => ['Identity']
            ],
            'forgot-password-verification' => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['Identity', 'OTP'], 'required'],
                    [['Identity'], 'email'],
                    [['OTP'], 'integer',],
                    ['OTP', 'compare', 'compareValue' => '123456', 'message' => 'Invalid OTP']
                ],
                'customValidation' => function () {
                    $identity = Project::getIdentity($this->appKey, \Yii::$app->request->post('Identity'));

                    if (!$identity) {
                        throw new HttpException(400, 'Identity not registered', [], [], 10010501);
                    }
                },
                'successName'      => 'Validation Success',
                'successMessage'   => 'Combination of Identity and OTP is valid',
                'successCode'      => 11010500,
                'successData'      => [
                    'ExpiredAt'     => Randomizer::futureDateTime(30, 30),
                    'AllowResendAt' => Randomizer::futureDateTime(5, 5),
                ],
            ],
            'reset-password'               => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['Identity', 'OTP', 'Password', 'ConfirmPassword'], 'required'],
                    [['Identity'], 'email'],
                    [['Password', 'ConfirmPassword'], 'string', 'min' => 8, 'max' => 32],
                    [['OTP'], 'integer',],
                    ['OTP', 'compare', 'compareValue' => '123456', 'message' => 'Invalid OTP'],
                    [
                        'ConfirmPassword',
                        'compare',
                        'compareAttribute' => 'Password',
                        'message'          => 'Password confirmation must be same as Password'
                    ],
                ],
                'customValidation' => function () {
                    $identity = Project::getIdentity($this->appKey, \Yii::$app->request->post('Identity'));

                    if (!$identity) {
                        throw new HttpException(400, 'Identity not registered', [], [], 10010601);
                    }
                },
                'successName'      => 'Reset Password Success',
                'successMessage'   => 'Password was reset successfully. All active devices must re-login to gain access',
                'successCode'      => 11010600,
                'successData'      => [],
            ],
            'forgot-password-resend'       => [
                'class'            => MockupFormAction::class,
                'rules'            => [
                    [['Identity'], 'required'],
                    [['Identity'], 'email'],
                ],
                'customValidation' => function () {
                    $identity = Project::getIdentity($this->appKey, \Yii::$app->request->post('Identity'));

                    if (!$identity) {
                        throw new HttpException(400, 'Identity not registered', [], [], 10010701);
                    }

                    if (rand(0, 1)) {
                        if (rand(0, 1)) {
                            throw new HttpException(400, 'Reset password never requested or expired', [], [], 10010702);
                        }

                        if (rand(0, 1)) {
                            throw new HttpException(400, 'Resend allowed after 5 minutes',
                                ["AllowResendAt" => Randomizer::futureDateTime(1, 4)], [], 10010703);
                        }

                        if (rand(0, 1)) {
                            throw new HttpException(400, 'You have reached maximum limit of resend OTP today',
                                ["AllowResendAt" => Randomizer::futureDateTime(1200, 1440)], [], 10010704);
                        }
                    }
                },
                'successName'      => 'OTP Resend Success',
                'successMessage'   => 'New OTP Code was sent to your email.',
                'successCode'      => 11010700,
                'successData'      => [],
            ]
        ];
    }

    protected function verbs()
    {
        return [
            'login'                        => ['post'],
            'forgot-password'              => ['post'],
            'forgot-password-verification' => ['post'],
            'reset-password'               => ['post'],
            'forgot-password-resend'       => ['post'],
        ];
    }

}