<?php


namespace api\controllers;


use api\components\Controller;
use api\components\HttpException;
use api\components\Response;
use api\filters\ContentTypeFilter;
use Carbon\Carbon;
use common\models\Event;
use common\models\EventQuestions;
use common\models\Participant;
use common\models\UserPoint;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class SelectEventController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'list',
                'detail'
            ]
        ];
        return $behaviors;
    }

    public function actionList()
    {
        $list = Participant::find()
            ->leftJoin(Event::tableName(), Participant::tableName() . '.eventId =' . Event::tableName() . '.id')
            ->where([
                Participant::tableName() . '.userId' => \Yii::$app->user->id,
                Participant::tableName() . '.status' => Participant::STATUS_ACTIVE,
                Event::tableName() . '.status'       => Event::STATUS_ACTIVE,
            ])
            ->andWhere(new \yii\db\Expression('NOW()') . '>=' . Event::tableName() . '.start')
            ->andWhere(new \yii\db\Expression('NOW()') . '<=' . Event::tableName() . '.end');

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
        $response->message = \Yii::t('app', 'Event loaded.');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, [
            Participant::class => [
                'id'            => function ($model) {
                    return $model->event->id;
                },
                'fileUrl'       => function ($model) {
                    if ($model->event->file) {
                        return $model->event->file->getSource();
                    }
                    return null;
                },
                'name'          => function ($model) {
                    return $model->event->name;
                },
                'description'   => function ($model) {
                    return Event::generateShort($model->event->description);
                },
                'announcement'  => function ($model) {
                    return Event::generateShort($model->event->announcement);
                },
                'start'         => function ($model) {
                    return \Yii::$app->formatter->asDatetime($model->event->start);
                },
                'end'           => function ($model) {
                    return \Yii::$app->formatter->asDatetime($model->event->end);
                },
                'latitude'      => function ($model) {
                    return $model->event->latitude;
                },
                'longitude'     => function ($model) {
                    return $model->event->longitude;
                },
                'status',
                'totalQuestion' => function ($model) {
                    $query = EventQuestions::find()->where([
                        EventQuestions::tableName() . '.status'  => EventQuestions::STATUS_ACTIVE,
                        EventQuestions::tableName() . '.eventId' => $model->id,
                    ])->count();
                    return $query;
                },
            ],
        ]);
        return $response;
    }

    public function actionDetail($id)
    {
        $detail = Participant::find()
            ->leftJoin(Event::tableName(), Participant::tableName() . '.eventId =' . Event::tableName() . '.id')
            ->where([
                Participant::tableName() . '.userId' => \Yii::$app->user->id,
                Participant::tableName() . '.status' => Participant::STATUS_ACTIVE,
                Event::tableName() . '.status'       => Event::STATUS_ACTIVE,
                Event::tableName() . '.id'           => $id,
            ])->andWhere([
                'and',
                ['<=', Event::tableName() . '.start', Carbon::now()->format('Y-m-d h:i:s')],
                ['>=', Event::tableName() . '.end', Carbon::now()->format('Y-m-d h:i:s')],
            ])->one();

        $totalPoint = (new \yii\db\Query())->from(UserPoint::tableName())->where([
            UserPoint::tableName() . '.userId' => \Yii::$app->user->id,
            UserPoint::tableName() . '.status' => UserPoint::STATUS_ACTIVE
        ])->sum('point');

        if (!$detail) {
            throw new HttpException(400, \Yii::t('app', 'Event not found'));
        }
        $response          = new Response();
        $response->status  = 200;
        $response->name    = \Yii::t('app', 'Event detail');
        $response->code    = 200;
        $response->message = \Yii::t('app', 'Get Event detail success');
        $detailData        = [
            'id'                => $detail->id,
            'fileId'            => $detail->event->fileId,
            'fileUrl'           => $detail->event->file->getSource(),
            'name'              => $detail->event->name,
            'description'       => $detail->event->description,
            'announcementShort' => Event::generateShort($detail->event->announcement),
            'announcement'      => $detail->event->announcement,
            'start'             => \Yii::$app->formatter->asDatetime($detail->event->start),
            'end'               => \Yii::$app->formatter->asDatetime($detail->event->end),
            'latitude'          => $detail->event->latitude,
            'longitude'         => $detail->event->longitude,
            'status'            => $detail->event->status,
            'totalPoint'        => isset($totalPoint) ? $totalPoint : 0,
        ];
        $response->data    = $detailData;
        \Yii::$app->session->set('eventId', $id);
        return $response;
    }

}