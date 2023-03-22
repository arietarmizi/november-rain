<?php


namespace api\controllers;


use api\components\Controller;
use api\components\Response;
use api\filters\ContentTypeFilter;
use common\models\UserPoint;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class PointHistoryController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'index'
            ]
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $point = UserPoint::find()
            ->where([
                UserPoint::tableName() . '.userId' => \Yii::$app->user->id,
                UserPoint::tableName() . '.status' => UserPoint::STATUS_ACTIVE
            ])->joinWith(['activity'])
            ->orderBy([UserPoint::tableName() . '.createdAt' => SORT_DESC]);

        $point->addOrderBy(['createdAt' => \SORT_DESC]);
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $point;
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
        $response->message = \Yii::t('app', 'User Point loaded.');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, [
            UserPoint::class => [
                'id',
                'activityId',
                'activity' => function ($model) {
                    return $model->activity->name;
                },
                'code',
                'point',
                'status'   => function ($model) {
                    return UserPoint::statuses()[$model->status];
                },
                'createdAt',
                'updatedAt'
            ],
        ]);
        $response->meta    = $meta;
        return $response;
    }

}