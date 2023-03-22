<?php
/**
 * Created by PhpStorm.
 * User: bupind
 * Date: 17/09/2019
 * Time: 7:02
 */

namespace common\models\auth;

use yii\helpers\ArrayHelper;
use common\base\ActiveRecord;

class AuthItem extends ActiveRecord
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    /**
     * @return mixed|\yii\db\Connection
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    public function getAuthItemChild()
    {
        return $this->hasMany(AuthItemChild::class, ['parent' => 'name']);
    }

    public static function roles($withDescription = false)
    {
        $roles = \Yii::$app->authManager->getRoles($withDescription = false);
        return ArrayHelper::map($roles, 'name', $withDescription ? function ($model) {
            return $model->name . ' : ' . $model->description;
        } : 'name');
    }

    public static function permissions($withDescription = false)
    {
        $permissions = \Yii::$app->authManager->getPermissions();
        return ArrayHelper::map($permissions, 'name', $withDescription ? function ($model) {
            return $model->name . ' : ' . $model->description;
        } : 'name');
    }
}
