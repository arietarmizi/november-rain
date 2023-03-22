<?php

namespace api\controllers;

use api\components\Controller;
use api\components\Response;
use api\filters\ContentTypeFilter;
use common\models\Region;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class DataController extends Controller
{

    public function behaviors()
    {
        $behaviors                            = parent::behaviors();
        $behaviors['content-type-filter']     = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'outlet',
                'bank'
            ]
        ];
        $behaviors['authenticator']['except'] = ['*'];
        return $behaviors;
    }

    public function actionRegion()
    {

        $postRequest = \Yii::$app->request->post();

        $query = Region::find();

        if (isset($postRequest['updatedAfter'])) {
            $query = $query->andWhere([
                'or',
                ['>=', 'createdAt', $postRequest['updatedAfter']],
                ['>=', 'updatedAt', $postRequest['updatedAfter']]
            ]);
        }

        if (isset($postRequest['territoryId'])) {
            $query = $query->andWhere(['territoryId' => $postRequest['territoryId']]);
        }

        $query->addOrderBy(['createdAt' => \SORT_DESC]);

        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $query;

        $getAll = (\Yii::$app->request->get('page') == 'all');

        if ($getAll) {
            $dataProvider->setPagination(false);
        }

        // get the result and pagination
        $result     = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();

        $meta = [
            'record' => [
                'current' => $dataProvider->getCount(),
                'total'   => $dataProvider->getTotalCount()
            ],
        ];

        if ($pagination instanceof Pagination) {
            $meta['page']  = [
                'current' => $pagination->getPage() + 1,
                'total'   => $pagination->getPageCount()
            ];
            $meta['links'] = $pagination->getLinks();
        }


        $response          = new Response();
        $response->name    = \Yii::t('app', 'Success');
        $response->status  = 200;
        $response->message = \Yii::t('app', 'Region loaded successfully');
        $response->code    = 0;
        $response->data    = $result;
        $response->meta    = $meta;

        return $response;
    }
}