<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/12/2020
 * Time: 5:37 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;


/**
 * Class Speaker
 *
 * @package common\models
 * @property string $id
 * @property string $fileId
 * @property string $name
 * @property string $gender
 * @property string $birthPlace
 * @property string $birthDate
 * @property string $nationality
 * @property string $maritalStatus
 * @property string $religion
 * @property string $address
 * @property string $phoneNumber
 * @property string $education
 * @property string $experience
 * @property string $achievement
 * @property string $profile
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property File    $file
 */
class Speaker extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    const MARITAL_STATUS_SINGLE  = 'single';
    const MARITAL_STATUS_MARRIED = 'married';
    const MARITAL_STATUS_OTHER   = 'other';

    const RELIGION_ISLAM      = 'islam';
    const RELIGION_CHRISTIAN  = 'christian';
    const RELIGION_BUDDHA     = 'buddha';
    const RELIGION_HINDU      = 'hindu';
    const RELIGION_KONG_HU_CU = 'kongHuCu';

    public static function tableName()
    {
        return '{{%speaker}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function genders()
    {
        return [
            self::GENDER_MALE   => \Yii::t('app', 'Male'),
            self::GENDER_FEMALE => \Yii::t('app', 'Female'),
        ];
    }

    public static function maritalStatuses()
    {
        return [
            self::MARITAL_STATUS_SINGLE  => \Yii::t('app', 'Single'),
            self::MARITAL_STATUS_MARRIED => \Yii::t('app', 'Married'),
            self::MARITAL_STATUS_OTHER   => \Yii::t('app', 'Other'),
        ];
    }

    public static function religions()
    {
        return [
            self::RELIGION_ISLAM      => \Yii::t('app', 'Islam'),
            self::RELIGION_CHRISTIAN  => \Yii::t('app', 'Christian'),
            self::RELIGION_BUDDHA     => \Yii::t('app', 'Buddha'),
            self::RELIGION_HINDU      => \Yii::t('app', 'Hindu'),
            self::RELIGION_KONG_HU_CU => \Yii::t('app', 'Kong Hu CU'),
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }
}