<?php

namespace common\models;


use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;

/**
 *
 * @property string $id
 * @property string $fileId
 * @property string $name
 * @property double $latitude
 * @property double $longitude
 * @property double $altitude
 * @property double $scoreFound
 * @property double $scoreBonus
 * @property string $appearedAt
 * @property string $disappearedAt
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 */
class Trivia extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }
}