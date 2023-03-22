<?php

namespace common\validators;

use yii\helpers\ArrayHelper;

/**
 * Description of PhoneNumberValidator
 *
 * @author Mohammad Nadzif <demihuman@live.com>
 */
class PhoneNumberValidator extends \yii\validators\StringValidator
{
    /** minimum length for phoneNumber */
    public $min = 9;

    /** maximum length for phoneNumber */
    public $max = 13;

    /**
     * accepted prefixes for this validation.
     * if prefixes set to an empty array, validation will allow all prefix and make validation true
     */
    public $prefixes = [
        4 => [
            '62811',
            '62812',
            '62813',
            '62814',
            '62815',
            '62816',
            '62817',
            '62818',
            '62819',
            '62821',
            '62822',
            '62823',
            '62828',
            '62831',
            '62838',
            '62851',
            '62852',
            '62853',
            '62855',
            '62856',
            '62857',
            '62858',
            '62859',
            '62877',
            '62878',
            '62879',
            '62881',
            '62882',
            '62883',
            '62884',
            '62887',
            '62888',
            '62889',
            '62895',
            '62896',
            '62897',
            '62898',
            '62899',
        ],
        5 => [
            '628681',
        ]
    ];

    /** variable for message whether phone number is not listed in any provider */
    public $notListed;

    public function init()
    {
        parent::init();

        /** set default message for not listed provider */
        if ($this->notListed == null) {
            $this->notListed = \Yii::t('app', '{attribute} not listed in any registered provider.');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if ($this->prefixes != []) {
            $hasProvider     = false;
            $attributePrefix = substr(preg_replace('(^08)', '628', $value), 0, 5);

            foreach (array_keys($this->prefixes) as $prefixSize) {
                if (ArrayHelper::isIn($attributePrefix, $this->prefixes[$prefixSize])) {
                    $hasProvider = true;
                    break;
                }
            }

            /** set error while there's phoneNumber has no provider */
            if (!$hasProvider) {
                $this->addError($model, $attribute, $this->notListed);
            }
        }

        parent::validateAttribute($model, $attribute);
    }

    protected function validateValue($value)
    {
        if ($this->prefixes != []) {
            $hasProvider     = false;
            $attributePrefix = substr(preg_replace('(^08)', '628', $value), 0, 5);

            foreach (array_keys($this->prefixes) as $prefixSize) {
                if (ArrayHelper::isIn($attributePrefix, $this->prefixes[$prefixSize])) {
                    $hasProvider = true;
                    break;
                }
            }

            /** set error while there's phoneNumber has no provider */
            if (!$hasProvider) {
                return [$this->notListed, []];
            }
        }

        return parent::validateValue($value);
    }
}