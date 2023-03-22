<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 9/5/2018
 * Time: 10:19 PM
 */

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use yii\helpers\StringHelper;

/**
 * Class Banner
 * @package common\models
 * @property string $id
 * @property string $fileId
 * @property string $subject
 * @property string $description
 * @property string $validFrom
 * @property string $validUntil
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 */
class Banner extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%banner}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'fileId'      => \Yii::t('app', 'Image'),
            'subject'     => \Yii::t('app', 'Subject'),
            'description' => \Yii::t('app', 'Description'),
            'validForm'   => \Yii::t('app', 'Valid From'),
            'validUntil'  => \Yii::t('app', 'Valid Until'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At'),
        ];
    }

    public function delete()
    {
        $transaction = self::getDb()->beginTransaction();
        $success     = true;
        // Delete file and record
        if ($file = $this->file) {
            $success &= $file->delete();
        }
        $success &= parent::delete();
        // Return
        $success ? $transaction->commit() : $transaction->rollBack();
        return $success;
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getFileSource()
    {
        return $this->file->getSource();
    }

    public static function descriptions($string)
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return $msg;
    }
    public static function generateShort($string, $length = null)
    {
        $length = $length ?: 50;
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }
}