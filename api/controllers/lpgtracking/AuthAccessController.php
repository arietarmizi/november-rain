<?php

namespace api\controllers\lpgtracking;

use api\components\HttpException;
use common\helpers\Project;
use common\helpers\Randomizer;
use yii\helpers\ArrayHelper;

class AuthAccessController extends Controller
{
    public function actionRefreshToken()
    {
        if (!\Yii::$app->request->headers->get('X-Device-Identifier')) {
            throw new HttpException(400, 'Device Identifier must be  defined.', [], [], 11010202);
        }

        $currentToken = str_replace('Bearer ', '', \Yii::$app->request->headers->get('Authorization'));

        if (Project::isBearerTokenValid($this->appKey, $currentToken)) {
            return [
                'Name'        => 'Refresh Token Success',
                'Message'     => 'Refresh Token Success',
                'Code'        => 10010200,
                'Status'      => 200,
                'Data'        => [
                    'User'   => [
                        'Id'          => \Yii::$app->user->identity->Id,
                        'Name'        => Randomizer::name(),
                        'Identity'    => \Yii::$app->user->identity->Identity,
                        'PhoneNumber' => Randomizer::phoneNumber(),
                        'Address'     => Randomizer::address(),
                        'IsVerified'  => rand(0, 1),
                    ],
                    'Device' => [
                        'AccessToken'   => \Yii::$app->user->identity->AccessToken,
                        'ExpiredAt'     => Randomizer::futureDateTime(),
                        'FirebaseToken' => \Yii::$app->user->identity->FirebaseToken,
                        'TimeZone'      => Randomizer::valueIn(['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura']),
                        'Language'      => 'id'
                    ],
                    'Role'   => [
                        'Name'            => \Yii::$app->user->identity->Role,
                        'AllowedPlatform' => \Yii::$app->user->identity->AllowedPlatform,
                    ],
                ],
                'Errors'      => [],
                'RequestTime' => Project::getRequestTime(),
                'Meta'        => [],
            ];
        }

        throw new HttpException(400, 'Your token is invalid or already expired. Please re-login to app', [], [],
            11010201);
    }

    public function actionLogout()
    {
        return [
            'Name'        => 'Log Out Success',
            'Message'     => 'Account logged out successfully',
            'Code'        => 10010300,
            'Status'      => 200,
            'Data'        => [],
            'Errors'      => [],
            'RequestTime' => Project::getRequestTime(),
            'Meta'        => [],
        ];
    }

    protected function verbs()
    {
        return [
            'refresh-token' => ['post'],
            'logout'        => ['post'],
        ];
    }

}