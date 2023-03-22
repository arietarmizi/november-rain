<?php
/**
 * Created by PhpStorm.
 * User: bupind
 * Date: 06/09/2019
 * Time: 18:57
 */

namespace common\models\auth;

use backend\base\ActiveRecord;

/**
 *
 * Class AuthAssignment
 *
 * @package common\models
 *
 * @property string $item_name
 * @property string $user_id
 * @property string $created_at
 *
 *
 */
class AuthAssignment extends ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }
}
