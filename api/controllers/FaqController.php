<?php

namespace api\controllers;

use common\models\Faq;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class FaqController extends Controller
{

    public function actionList()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;

        $faq = Faq::find()->where([Faq::tableName() . '.status' => Faq::STATUS_ACTIVE])
            ->orderBy('sequence ASC')->asArray()->all();

        return $this->render('/faq/_detail', [
            'title' => Yii::t('app', 'Faq'),
            'faq'   => $faq
        ]);
    }

    protected function verbs()
    {
        return [
            'list' => ['get'],
        ];
    }
}