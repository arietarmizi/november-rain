<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 8/11/2018
 * Time: 4:45 AM
 */

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\auth\ForgotPasswordForm;
use api\forms\auth\LoginForm;
use api\forms\auth\RegisterForm;
use api\forms\auth\ResetPasswordForm;
use common\models\Device;
use common\models\User;
use Yii;
use yii\web\ForbiddenHttpException;

class AuthController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = [
            'register',
            'login',
            'forgot-password',
            'reset-password'
        ];

        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'register',
                'resend-verification-code',
                'login',
                'logout',
                'change-phone-number',
                'forgot-password',
                'reset-password'
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'register'        => [
                'class'          => FormAction::class,
                'formClass'      => RegisterForm::class,
                'messageSuccess' => Yii::t('app', 'Registration success'),
                'messageFailed'  => Yii::t('app', 'Registration failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'login'           => [
                'class'          => FormAction::class,
                'formClass'      => LoginForm::class,
                'messageSuccess' => Yii::t('app', 'Login success'),
                'messageFailed'  => Yii::t('app', 'Login failed'),
                'apiCodeSuccess' => ApiCode::USER_LOGIN_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_LOGIN_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'forgot-password' => [
                'class'          => FormAction::class,
                'formClass'      => ForgotPasswordForm::class,
                'messageSuccess' => Yii::t('app', 'Request reset password success'),
                'messageFailed'  => Yii::t('app', 'Request for forgot password failed'),
                'apiCodeSuccess' => ApiCode::USER_FORGOT_PASSWORD_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_FORGOT_PASSWORD_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'reset-password'  => [
                'class'          => FormAction::class,
                'formClass'      => ResetPasswordForm::class,
                'messageSuccess' => Yii::t('app', 'Reset password success'),
                'messageFailed'  => Yii::t('app', 'Reset password failed'),
                'apiCodeSuccess' => ApiCode::USER_RESET_PASSWORD_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_RESET_PASSWORD_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }


    /**
     * @return Response
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @since 2018-05-14 06:08:33
     */

    public function actionLogout()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $currentDevice         = $user->currentDevice;
        $currentDevice->status = Device::STATUS_INACTIVE;
        $currentDevice->save();

        $response          = new Response();
        $response->name    = Yii::t('app', 'Logout Success');
        $response->message = Yii::t('app', 'You have logout successfully from current device.');
        $response->code    = ApiCode::USER_LOGIN_SUCCESS;
        $response->status  = 200;
        $response->data    = [];

        return $response;
    }

    protected function verbs()
    {
        return [
            'register'            => ['post'],
            'login'               => ['post'],
            'logout'              => ['post'],
            'change-phone-number' => ['post'],
            'forgot-password'     => ['post'],
            'reset-password'      => ['post'],
        ];
    }

}