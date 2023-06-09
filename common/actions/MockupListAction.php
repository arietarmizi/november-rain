<?php

namespace common\actions;

use common\helpers\Project;
use yii\base\Action;
use yii\helpers\ArrayHelper;

class MockupListAction extends Action
{
    public $rules;

    public $pageKey    = 'page';
    public $perPageKey = 'per-page';

    public $successName;
    public $successMessage;
    public $successCode;

    public $data;

    private $_queryParams = [];
    private $_page;
    private $_perPage;

    protected function beforeRun()
    {
        $this->_queryParams = \Yii::$app->request->queryParams;
        $this->_page        = (int)ArrayHelper::getValue($this->_queryParams, $this->pageKey);
        $this->_perPage     = (int)ArrayHelper::getValue($this->_queryParams, $this->perPageKey);

        return parent::beforeRun(); // TODO: Change the autogenerated stub
    }

    private function getMeta()
    {
        $route = \Yii::$app->controller->route;

        $selfLink = [$route, $this->pageKey => $this->_page, $this->perPageKey => $this->_perPage];

        if ($this->_page <= 1) {
            $prevLink = null;
        } else {
            if ($this->_page > 5) {
                $prevLink = [$route, $this->pageKey => 5, $this->perPageKey => $this->_perPage];
            } else {
                $prevLink = [$route, $this->pageKey => $this->_page - 1, $this->perPageKey => $this->_perPage];
            }
        }

        if ($this->_page >= 5) {
            $nextLink = null;
        } else {
            if ($this->_page < 1) {
                $nextLink = [$route, $this->pageKey => 1, $this->perPageKey => $this->_perPage];
            } else {
                $nextLink = [$route, $this->pageKey => $this->_page + 1, $this->perPageKey => $this->_perPage];
            }
        }


        $firstLink = [$route, $this->pageKey => 1, $this->perPageKey => $this->_perPage];
        $lastLink  = [$route, $this->pageKey => 5, $this->perPageKey => $this->_perPage];

        return [
            'Record' => [
                'Current' => $this->_perPage,
                'Total'   => $this->_perPage * 5,
            ],
            'Page'   => [
                'Current' => $this->_page,
                'Total'   => 5,
            ],
            'Links'  => [
                'Self'  => $selfLink ? \Yii::$app->urlManager->createUrl($selfLink) : null,
                'Prev'  => $prevLink ? \Yii::$app->urlManager->createUrl($prevLink) : null,
                'Next'  => $nextLink ? \Yii::$app->urlManager->createUrl($nextLink) : null,
                'First' => $firstLink ? \Yii::$app->urlManager->createUrl($firstLink) : null,
                'Last'  => $lastLink ? \Yii::$app->urlManager->createUrl($lastLink) : null,
            ],
        ];
    }

    public function run()
    {
        $data = [];

        for ($iC = 0; $iC < $this->_perPage; $iC++) {
            if (is_callable($this->data)) {
                $data[] = \call_user_func($this->data, $iC);
            }
        }

        return [
            'Name'        => $this->successName,
            'Message'     => $this->successMessage,
            'Code'        => $this->successCode,
            'Status'      => 200,
            'Data'        => $data,
            'Errors'      => [],
            'RequestTime' => Project::getRequestTime(),
            'Meta'        => $this->getMeta(),
        ];
    }
}