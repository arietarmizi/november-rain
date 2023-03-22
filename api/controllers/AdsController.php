<?php

namespace api\controllers;

use api\components\Controller;
use api\components\HttpException;
use api\components\Response;
use api\filters\ContentTypeFilter;
use Carbon\Carbon;
use common\models\Ads;
use common\models\AdsShow;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;


class AdsController extends Controller
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
        $user     = \Yii::$app->user->identity;
        $dataList = Ads::find()
            ->leftJoin(AdsShow::tableName(),
                Ads::tableName() . ".id = " . AdsShow::tableName() . '.adsid')
            ->where([Ads::tableName() . '.status' => Ads::STATUS_ACTIVE])
            ->andWhere([
                'and',
                ['<=', Ads::tableName() . '.validFrom', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
                ['>=', Ads::tableName() . '.validUntil', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
            ]);
        $dataList->andWhere([AdsShow::tableName() . '.regionId' => $user->regionId]);

        $dataList->addOrderBy([Ads::tableName() . '.createdAt' => SORT_DESC]);
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $dataList;
        $getAll              = (Yii::$app->request->get('page') == 'all');

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
        $response->message = Yii::t('app', 'ADS loaded.');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, [
            Ads::class => [
                'id',
                'fileId',
                'fileUrl'     => function ($model) {
                    if ($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'subject'     => function ($model) {
                    return Ads::generateShort($model->subject);
                },
                'description' => function ($model) {
                    return Ads::generateShort($model->description);
                },
                'validFrom'   => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->validFrom);
                },
                'validUntil'  => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->validUntil);
                },
                'showTo',
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
        $detail = Ads::find()->where([Ads::tableName() . '.id' => $id])->one();

        if (!$detail) {
            throw new HttpException(400, Yii::t('app', 'Ads not found'));
        }
        $response          = new Response();
        $response->status  = 200;
        $response->name    = Yii::t('app', 'Ads detail');
        $response->code    = 200;
        $response->message = Yii::t('app', 'Get Ads detail success');
        $detailData        = [
            'id'          => $detail->id,
            'subject'     => $detail->subject,
            'fileId'      => $detail->fileId,
            'fileUrl'     => isset($detail->file) ? $detail->file->getSource() : null,
            'description' => Ads::descriptions($detail->description),
            'validFrom'   => $detail->validFrom,
            'validUntil'  => $detail->validUntil,
            'status'      => $detail->status,
            'createdAt'   => $detail->createdAt,
            'updatedAt'   => $detail->updatedAt
        ];
        $response->data    = $detailData;
        return $response;
    }

    protected function verbs()
    {
        return [
            'list'   => ['get'],
            'detail' => ['get'],
        ];
    }
}