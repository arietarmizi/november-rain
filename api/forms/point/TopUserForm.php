<?php

namespace api\forms\point;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Company;
use common\models\Event;
use common\models\User;
use common\models\UserPoint;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TopUserForm extends BaseForm
{
    public $eventId;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required'],
            [['pageSize'], 'number'],
        ];
    }

    public function submit()
    {
        $query = UserPoint::find()
            ->select([
                new Expression('SUM(point) AS totalPoint'),
                User::tableName() . '.id',
                User::tableName() . '.name',
                Company::tableName() . '.id AS companyId',
                Company::tableName() . '.name AS companyName'
            ])
            ->joinWith(['activity'])->where([
                UserPoint::tableName() . '.status' => UserPoint::STATUS_ACTIVE,
                Event::tableName() . '.id'         => $this->eventId,
            ])
            ->innerJoin(User::tableName(), User::tableName() . '.id=' . UserPoint::tableName() . '.userId')
            ->innerJoin(Company::tableName(), Company::tableName() . '.id=' . User::tableName() . '.companyId')
            ->groupBy([
                User::tableName() . '.id',
                User::tableName() . '.name',
                Company::tableName() . '.id',
                Company::tableName() . '.name'
            ])->orderBy(['totalPoint' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        $getAll = (\Yii::$app->request->get('page') == 'all');

        if ($getAll) {
            $dataProvider->setPagination(false);
        }

        $this->_items = $dataProvider->getModels();
        $pagination   = $dataProvider->getPagination();

        $this->_meta = [
            'record' => [
                'current' => $dataProvider->getCount(),
                'total'   => $dataProvider->getTotalCount()
            ],
        ];

        if ($pagination instanceof Pagination) {
            $this->_meta['page']  = [
                'current' => $pagination->getPage() + 1,
                'total'   => $pagination->getPageCount()
            ];
            $this->_meta['links'] = $pagination->getLinks();
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            UserPoint::class => [
                'id',
                'totalPoint',
                'name',
                'companyName'
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }

    public function meta()
    {
        return $this->_meta;
    }
}