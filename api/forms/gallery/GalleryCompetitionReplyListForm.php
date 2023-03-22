<?php

namespace api\forms\gallery;

use abei2017\emoji\Emoji;
use api\components\BaseForm;
use common\models\News;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class GalleryCompetitionReplyListForm extends BaseForm
{
    public $photoCompetitionId;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['photoCompetitionId'], 'required'],
            [['pageSize'], 'number'],
        ];
    }

    public function submit()
    {
        $query        = PhotoCompetitionReply::find()->where([
            PhotoCompetitionReply::tableName() . '.photoCompetitionId' => $this->photoCompetitionId,
            PhotoCompetitionReply::tableName() . '.status'             => PhotoCompetition::STATUS_ACTIVE,
        ])->addOrderBy([PhotoCompetitionReply::tableName() . '.createdAt' => \SORT_ASC]);
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
            PhotoCompetitionReply::class => [
                'id',
                'userName'        => function ($model) {
                    return isset($model->user) ? $model->user->name : null;
                },
                'userProfile' => function ($model) {
                    if ($model->user->file) {
                        return $model->user->file->getSource();
                    }
                    return null;
                },
                'description'     => function ($model) {
                    return News::generateShort(Emoji::toImage($model->reply));
                },
                'reply'           => function ($model) {
                    return Emoji::toImage($model->reply);
                },
                'createdAt',
                'updatedAt'
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