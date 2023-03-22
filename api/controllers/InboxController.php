<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\components\HttpException;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\inbox\UserInboxForm;
use common\models\UserInbox;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class InboxController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'change-status',
                'list'
            ]
        ];

        return $behaviors;
    }

    public function actionList()
    {
        $list = UserInbox::find()
            ->where(['userId' => Yii::$app->user->id])
            ->andWhere([
                'in',
                UserInbox::tableName() . '.status',
                [
                    UserInbox::STATUS_SENT,
                    UserInbox::STATUS_READ,
                    UserInbox::STATUS_ARCHIVED
                ]
            ]);

        $list->addOrderBy(['createdAt' => \SORT_DESC]);
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $list;
        $getAll              = (\Yii::$app->request->get('page') == 'all');

        if ($getAll) {
            $dataProvider->setPagination(false);
        }

        // get the result and pagination
        $result     = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();
        $meta       = [
            'record' => [
                'current' => $dataProvider->getCount(),
                'total'   => $dataProvider->getTotalCount(),
            ],
        ];

        if ($pagination instanceof Pagination) {
            $meta['page']  = [
                'current' => $pagination->getPage() + 1,
                'total'   => $pagination->getPageCount(),
            ];
            $meta['links'] = $pagination->getLinks();
        }

        $response          = new Response();
        $response->name    = 'Success';
        $response->status  = 200;
        $response->message = \Yii::t('app', 'UserInbox loaded.');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, [
            UserInbox::class => [
                'id',
                'category',
                'title',
                'shortMessage',
                'status',
                'createdAt',
                'updatedAt'
            ],
        ]);
        $response->meta    = $meta;
        return $response;
    }

    public function actionDetail($id)
    {
        $detail = UserInbox::find()->where(['userId' => Yii::$app->user->id, 'id' => $id])->one();

        if (!$detail) {
            throw new HttpException(400, \Yii::t('app', 'UserInbox not found'));
        }

        $response          = new Response();
        $response->name    = \Yii::t('app', 'UserInbox detail');
        $response->status  = 200;
        $response->message = \Yii::t('app', 'Get UserInbox detail success');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($detail, [
            UserInbox::class => [
                'userId',
                'category',
                'title',
                'message',
                'status',
                'createdAt',
                'updatedAt'
            ],
        ]);

        $response->meta = [];

        return $response;
    }

    public function actions()
    {
        return [
            'change-status' => [
                'class'          => FormAction::class,
                'formClass'      => UserInboxForm::class,
                'messageSuccess' => \Yii::t('app', 'Change message status success'),
                'messageFailed'  => \Yii::t('app', 'Change message status  failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'change-status' => ['post'],
            'list'          => ['get']
        ];
    }
}
                