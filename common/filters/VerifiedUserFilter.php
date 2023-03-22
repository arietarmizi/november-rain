<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 10/4/2018
 * Time: 3:38 PM
 */

namespace common\filters;


use common\models\User;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class VerifiedUserFilter extends ActionFilter
{

    public function beforeAction($action)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!$user->verified) {
            throw new ForbiddenHttpException(\Yii::t('app', 'Please verify your account.'));
        }

        return parent::beforeAction();
    }

}