<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;

/**
 * Class Company
 *
 * @package common\models
 * @property string $id
 * @property string $name
 * @property string $phoneNumber
 * @property string $email
 * @property string $instagram
 * @property string $fileId
 * @property string $companyHead
 * @property string $since
 * @property string $address
 * @property string $description
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 */
class Company extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%company}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getTotalPeople()
    {
        return $this->hasMany(User::class, ['companyId' => 'id'])->count();
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'Id'),
            'name'        => \Yii::t('app', 'Name'),
            'description' => \Yii::t('app', 'Description'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At'),
        ];
    }

}
