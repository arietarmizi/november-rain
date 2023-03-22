<?php


namespace api\controllers;

use api\components\Controller;
use api\components\HttpException;
use api\components\Response;
use Carbon\Carbon;
use common\models\Banner;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class BannerController extends Controller
{
    public function behaviors()
    {
        $behaviors                              = parent::behaviors();
        $behaviors['authenticator']['except']   = ['list', 'detail'];
        $behaviors['systemAppFilter']['except'] = ['list', 'detail'];
        return $behaviors;
    }

    public function actionList()
    {
        $data = Banner::find()->where(['status' => Banner::STATUS_ACTIVE])
            ->andWhere([
                'and',
                ['<=', Banner::tableName() . '.validFrom', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
                ['>=', Banner::tableName() . '.validUntil', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
            ]);

        $data->addOrderBy(['createdAt' => \SORT_DESC]);
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $data;
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
        $response->message = \Yii::t('app', 'Banner loaded.');
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, [
            Banner::class => [
                'id',
                'fileId',
                'fileUrl'     => function ($model) {
                    if ($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'subject'     => function ($model) {
                    return Banner::generateShort($model->subject);
                },
                'description' => function ($model) {
                    return Banner::generateShort($model->description);
                },
                'validFrom'   => function ($model) {
                    return \Yii::$app->formatter->asDatetime($model->validFrom);
                },
                'validUntil'  => function ($model) {
                    return \Yii::$app->formatter->asDatetime($model->validUntil);
                },
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
        $detail = Banner::find()->where([Banner::tableName() . '.id' => $id])->one();

        if (!$detail) {
            throw new HttpException(400, \Yii::t('app', 'Banner not found'));
        }
        $response          = new Response();
        $response->status  = 200;
        $response->name    = \Yii::t('app', 'Banner detail');
        $response->code    = 200;
        $response->message = \Yii::t('app', 'Get Banner detail success');
        $detailData        = [
            'id'          => $detail->id,
            'subject'     => $detail->subject,
            'fileId'      => $detail->fileId,
            'fileUrl'     => isset($detail->file) ? $detail->file->getSource() : null,
            'description' => Banner::descriptions($detail->description),
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