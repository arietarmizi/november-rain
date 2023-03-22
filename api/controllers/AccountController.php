<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 8/15/2018
 * Time: 1:23 AM
 */

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\account\ChangePasswordForm;
use api\forms\account\EditProfileForm;
use api\forms\account\SetImageForm;
use api\forms\account\UserContactForm;
use common\models\Device;
use common\models\User;
use common\models\UserContact;
use common\models\UserPoint;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class AccountController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'change-password',
                'add-contact',
                'edit-profile',
                'remove-contact',
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'change-password'      => [
                'class'          => FormAction::class,
                'formClass'      => ChangePasswordForm::class,
                'messageSuccess' => \Yii::t('app', 'Yout password changed successfully.'),
                'messageFailed'  => \Yii::t('app', 'Failed while changing password'),
                'apiCodeSuccess' => ApiCode::USER_FORGOT_PASSWORD_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_FORGOT_PASSWORD_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'edit-profile'         => [
                'class'          => FormAction::class,
                'formClass'      => EditProfileForm::class,
                'messageSuccess' => \Yii::t('app', 'Profile updated.'),
                'messageFailed'  => \Yii::t('app', 'Failed while updating profile'),
                'apiCodeSuccess' => ApiCode::USER_FORGOT_PASSWORD_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_FORGOT_PASSWORD_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'edit-profile-picture' => [
                'class'          => FormAction::class,
                'formClass'      => SetImageForm::class,
                'messageSuccess' => \Yii::t('app', 'Profile Picture Updated.'),
                'messageFailed'  => \Yii::t('app', 'Failed while updating profile Picture'),
                'apiCodeSuccess' => ApiCode::USER_FORGOT_PASSWORD_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_FORGOT_PASSWORD_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'add-contact'          => [
                'class'          => FormAction::class,
                'formClass'      => UserContactForm::class,
                'messageSuccess' => \Yii::t('app', 'Add User contact success'),
                'messageFailed'  => \Yii::t('app', 'Add User contact failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ]
        ];
    }

    public function actionRemoveSession()
    {
        Device::updateAll(['status' => Device::STATUS_INACTIVE], [
            'and',
            ['userId' => \Yii::$app->user->id, 'status' => Device::STATUS_ACTIVE],
            ['!=', 'identifier', \Yii::$app->request->headers->get('X-Device-identifier', null)],
        ]);
        $response          = new Response();
        $response->status  = 200;
        $response->name    = \Yii::t('app', 'Remove Session Success');
        $response->code    = ApiCode::CHECK_POINT_SUCCESS;
        $response->message = \Yii::t('app', 'Other session removed successfully.');
        $response->data    = [];

        return $response;
    }

    public function actionProfile()
    {
        /** @var User $user */
        $user = User::find()->where([
            User::tableName() . '.id'     => \Yii::$app->user->id,
            User::tableName() . '.status' => User::STATUS_ACTIVE,
        ])->joinWith(['contact', 'file'])->one();

        $users = (new Query())
            ->select([
                User::tableName() . '.id',
                'SUM(' . UserPoint::tableName() . '.point) as totalPoint'
            ])->from(User::tableName())
            ->where([User::tableName() . '.id' => \Yii::$app->user->id])
            ->leftJoin(UserPoint::tableName(),
                UserPoint::tableName() . '.userId = ' . User::tableName() . '.id AND (' .
                UserPoint::tableName() . '.status = \'' . UserPoint::STATUS_ACTIVE . '\')'
            )
            ->groupBy([User::tableName() . '.id'])
            ->all(\Yii::$app->db);

        $userTotalPoints = ArrayHelper::getColumn($users, 'totalPoint');
        $sumPoint        = array_sum($userTotalPoints);

        $queryContact = UserContact::find()->where(['userId' => $user->id])->all();
        $contact      = [];
        foreach ($queryContact as $item => $val) {
            $contact[] = [
                'id'           => $val->id,
                'relationship' => $val->relationship,
                'phoneNumber'  => $val->phoneNumber,
            ];
        }

        if ($user) {
            $response          = new Response();
            $response->status  = 200;
            $response->name    = \Yii::t('app', 'User Profile');
            $response->code    = 200;
            $response->message = \Yii::t('app', 'Load User Profile Success.');
            $profile           = [
                'id'                 => $user->id,
                'companyId'          => $user->company->name,
                'companyAddress'     => isset($user->company) ? $user->company->address : null,
                'companyDescription' => isset($user->company) ? $user->company->description : null,
                'companyPhoneNumber' => isset($user->company) ? $user->company->phoneNumber : null,
                'companyEmail'       => isset($user->company) ? $user->company->email : null,
                'companyInstagram'   => isset($user->company) ? $user->company->instagram : null,
                'companyImage'       => isset($user->company->file) ? $user->company->file->getSource() : null,
                'companyHead'        => isset($user->company) ? $user->company->companyHead : null,
                'memberId'           => $user->memberId,
                'covidStatus'        => $user->covidStatus,
                'identityCardNumber' => $user->identityCardNumber,
                'type'               => $user->type,
                'name'               => $user->name,
                'phoneNumber'        => $user->phoneNumber,
                'email'              => $user->email,
                'birthPlace'         => $user->birthPlace,
                'birthDate'          => $user->birthDate,
                'position'           => $user->position,
                'verified'           => $user->verified,
                'fileId'             => $user->fileId,
                'fileUrl'            => isset($user->file) ? $user->file->getSource() : null,
                'regionId'           => $user->regionId,
                'regionName'         => isset($user->region) ? $user->region->name : null,
                'regionDescription'  => isset($user->region) ? $user->region->description : null,
                'picName'            => isset($user->region) ? $user->region->picName : null,
                'picPhoneNumber'     => isset($user->region) ? $user->region->picPhoneNumber : null,
                'picEmail'           => isset($user->region) ? $user->region->picEmail : null,
                'picAddress'         => isset($user->region) ? $user->region->picAddress : null,
                'segmen'             => $user->segmen,
                'grade'              => $user->grade,
                'instagram'          => $user->instagram,
                'address'            => $user->address,
                'hobby'              => $user->hobby,
                'blood'              => $user->blood,
                'userTotalPoints'    => isset($sumPoint) ? $sumPoint : 0,
                'status'             => $user->status,
                'createdAt'          => $user->createdAt,
                'updatedAt'          => $user->updatedAt,
                'userContact'        => $contact
            ];
            $response->data    = $profile;

            return $response;
        }

        throw new NotFoundHttpException(\Yii::t('app', 'Could not find user.'), 1003);
    }

    public function actionRemoveContact($id)
    {
        $rowsDeleted = UserContact::findOne([
            'id'     => $id,
            'userId' => \Yii::$app->user->id,
        ]);
        if (!$rowsDeleted) {
            throw new HttpException(400, \Yii::t('app', 'User Contact not found'));
        }
        $rowsDeleted->delete();
        $response          = new Response();
        $response->status  = 200;
        $response->name    = \Yii::t('app', 'User Contact Removed');
        $response->code    = 200;
        $response->message = \Yii::t('app', 'Removed User Contact success');
        $response->data    = [];
        return $response;
    }

    public function actionPoint()
    {
        $totalPoint = UserPoint::find()
            ->select('SUM(point) as totalPoint')
            ->where(['userId' => \Yii::$app->user->id, 'status' => UserPoint::STATUS_ACTIVE])
            ->groupBy(['userId'])
            ->asArray()
            ->one();

        $response          = new Response();
        $response->status  = 200;
        $response->name    = \Yii::t('app', 'User Point Success');
        $response->code    = 200;
        $response->message = \Yii::t('app', 'User point loaded');
        $response->data    = (double)ArrayHelper::getValue($totalPoint, 'totalPoint');
        return $response;
    }

    protected function verbs()
    {
        return [
            'remove-session'       => ['get'],
            'change-password'      => ['post'],
            'edit-profile'         => ['post'],
            'edit-profile-picture' => ['post'],
            'add-contact'          => ['post'],
            'profile'              => ['get'],
            'remove-contact'       => ['get'],
            'point'                => ['get'],
        ];
    }
}
