<?php

namespace common\base;

use Ramsey\Uuid\Uuid;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Class ActiveRecord
 *
 * @package common\base
 * @author  Haqqi <me@haqqi.net>
 *
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    public static function tableColumns()
    {
        $columnNames = self::getTableSchema()->getColumnNames();

        $columnWithTableName = [];
        foreach ($columnNames as $columnName) {
            $columnWithTableName[] = self::tableName() . '.' . $columnName;
        }

        return $columnWithTableName;
    }

    public static function nextOrPrev($currentId, $nextOrPrev = 'next')
    {
        $order   = ($nextOrPrev === 'next' ? self::tableName() . '.id ASC' : self::tableName() . '.id DESC');
        $records = self::find()->orderBy($order)->all();
        foreach ($records as $index => $record) {
            if ($record->id === $currentId) {
                return isset($records[$index + 1]->id) ? $records[$index + 1]->id : NULL;

            }
        }
        return FALSE;
    }

    /**
     * Default behaviors for all models in this project
     *
     * @return array
     */

    public function behaviors()
    {
        $behaviors = [];

        $behaviors['timestampBehavior'] = [
            'class'              => TimestampBehavior::class,
            'value'              => new Expression("'" . date('Y-m-d H:i:s') . "'"),
            'createdAtAttribute' => 'createdAt',
            'updatedAtAttribute' => 'updatedAt'
        ];

        $behaviors['uuid'] = [
            'class'      => AttributeBehavior::class,
            'value'      => function ($event) {
                return Uuid::uuid4()->toString();
            },
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['id']
            ],
        ];


        return $behaviors;
    }
}
