<?php
/**
 * Created by PhpStorm.
 * User: bupind
 * Date: 17/09/2019
 * Time: 7:03
 */

namespace common\models\auth;
use common\base\ActiveRecord;

class AuthItemChild extends ActiveRecord
{

    /**
     * @return mixed|\yii\db\Connection
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }

    public function getAuthItem()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'parent']);
    }

    public function delete($runValidation = true)
    {
        $authManager        = \Yii::$app->authManager;
        $transaction        = \Yii::$app->getDb()->beginTransaction();
        $transactionSuccess = true;
        try {
            $userPermission = $authManager->getPermission(\Yii::$app->request->get('child'));
            $userRole       = $authManager->getRole(\Yii::$app->request->get('name'));
            $authManager->removeChild($userRole, $userPermission);
            $transactionSuccess ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $transactionSuccess = false;
        }
        return $transactionSuccess;
    }
}
